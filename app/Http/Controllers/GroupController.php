<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PostAttachment;
use Illuminate\Validation\Rule;
use App\Notifications\RoleChange;
use App\Notifications\GroupRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Enums\GroupUserRoleEnum;
use App\Http\Resources\GroupResource;
use App\Providers\AppServiceProvider;
use App\Notifications\GroupInvitation;
use App\Notifications\RequestApproved;
use App\Http\Enums\GroupUserStatusEnum;
use App\Notifications\RemovedFromGroup;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\InviteUsersRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\PostAttachmentResource;
use App\Notifications\ApprovedInvitation;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GroupController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

        $myGroups = Group::query()
            ->select(['groups.*', 'gu.status', 'gu.role'])
            ->join('group_users AS gu', 'gu.group_id', 'groups.id')
            ->where('gu.user_id', $userId)
            ->orderBy('gu.role')
            ->orderBy('name', 'desc')
            ->get();

        return Inertia::render('Group/MyGroups', [
            'groups' => GroupResource::collection($myGroups),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function profile(Request $request, Group $group)
    {
        $group->load('currentUserGroup');

        $userId = Auth::id();

        if ($group->hasApprovedUser($userId)) {
            $posts = Post::postsForTimeline($userId)
                ->where('group_id', $group->id)
                ->paginate(10);

            $posts = PostResource::collection($posts);
        } else {

            return Inertia::render('Group/View', [
                'success' => session('success'),
                'group' => new GroupResource($group),
                'posts' => null,
                'users' => [],
                'requests' => []
            ]);
        }

        if ($request->wantsJson()) {
            return $posts;
        }

        $users = User::query()
            ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
            ->join('group_users AS gu', 'gu.user_id', 'users.id')
            ->orderBy('users.name')
            ->where('group_id', $group->id)
            ->get();

        $requests = $group->pendingUsers()->orderBy('name')->get();

        $photos = PostAttachment::query()
            ->select('post_attachments.*')
            ->join('posts AS p', 'p.id', 'post_attachments.post_id')
            ->where('p.group_id', $group->id)
            ->where('mime', 'like', 'image/%')
            ->latest()
            ->get();

        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group),
            'posts' => $posts,
            'users' => GroupUserResource::collection($users),
            'requests' => UserResource::collection($requests),
            'photos' => PostAttachmentResource::collection($photos),
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Group::create($data);

        $groupUserData = [
            'status' => GroupUserStatusEnum::APPROVED->value,
            'role' => GroupUserRoleEnum::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ];

        GroupUser::create($groupUserData);
        $group->status = $groupUserData['status'];
        $group->role = $groupUserData['role'];

        return response(new GroupResource($group), 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return back()->with('success', 'Group has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }

    public function updateImage(Request $request, Group $group)
    {

        if (!$group->isAdmin(Auth::id())) {
            return response("You do not have permission to perform this action", 403);
        };

        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'thumbnail' => ['nullable', 'image']
        ]);

        $thumbnail = $data['thumbnail'] ?? null;
        // @var \Illuminate\Http\UploadedFile $cover
        $cover = $data['cover'] ?? null;

        $success = '';

        if ($cover) {

            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }

            $path = $cover->store('group-' . $group->id, 'public');
            $group->update(['cover_path' => $path]);
            $success = 'Your Cover Image Has Succcessfully Been Updated';
        }

        if ($thumbnail) {

            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }

            $path = $thumbnail->store('group-' . $group->id, 'public');
            $group->update(['thumbnail_path' => $path]);
            $success = 'Your thumbnail Image Has Succcessfully Been Updated';
        }

        return back()->with('success', $success);
    }

    public function inviteUsers(InviteUsersRequest $request, Group $group)
    {

        $data = $request->validated();

        $user = $request->user;

        $groupUser = $request->groupUser;


        // Delete existing invite when invitaed more than once and generate new one
        if ($groupUser) {
            $groupUser->delete();
        };

        $hours = 24;
        $token = Str::random(256);

        GroupUser::create([
            'status' => GroupUserStatusEnum::PENDING->value,
            'role' => GroupUserRoleEnum::USER->value,
            'token' => $token,
            'token_expiry_date' => Carbon::now()->addHours($hours),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => Auth::id(),
        ]);

        $user->notify(new GroupInvitation($group, $hours, $token));

        return back()->with('success', 'Invite sent!');
    }

    public function approveInvitation(string $token)
    {
        $groupUser = GroupUser::query()
            ->where('token', $token)
            ->first();

        $errorTitle = '';

        if (!$groupUser) {
            $errorTitle = 'The token is invalid';
        } else if ($groupUser->token_used || $groupUser->status === GroupUserStatusEnum::APPROVED->value) {
            $errorTitle = 'The link has already been used';
        } else if ($groupUser->token_expiry_date < Carbon::now()) {
            $errorTitle = 'The link has expired';
        }

        if ($errorTitle) {
            return Inertia::render('Error', compact('errorTitle'));
        }

        $groupUser->status = GroupUserStatusEnum::APPROVED->value;
        $groupUser->token_used = Carbon::now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;

        $adminUser->notify(new ApprovedInvitation($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))->with('success', 'You are now a member of ' . $groupUser->group->name . '. Welcome!');
    }

    public function join(Group $group)
    {
        $user = \request()->user();

        $status =  GroupUserStatusEnum::APPROVED->value;

        $successMessage = 'You are now a member of ' . $group->name . '. Welcome!';

        if (!$group->auto_approval) {
            $status =  GroupUserStatusEnum::PENDING->value;

            Notification::send($group->adminUsers, new GroupRequest($group, $user));
            $successMessage = 'Request Sent! You Will Be Notified When Your Request Is Approved';
        }

        GroupUser::create([
            'status' => $status,
            'role' => GroupUserRoleEnum::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id,
        ]);

        return back()->with('success', $successMessage);
    }

    public function approveRequest(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You do not have permission to perform this action");
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'action' => ['required'],
        ]);

        $groupUser =  GroupUser::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatusEnum::PENDING->value)
            ->first();

        if ($groupUser) {
            $approved = false;
            if ($data['action'] === 'approve') {
                $approved = true;
                $groupUser->status = GroupUserStatusEnum::APPROVED->value;
            } else {
                $groupUser->status = GroupUserStatusEnum::REJECTED->value;
            }
            $groupUser->save();

            $user = $groupUser->user;

            $user->notify(new RequestApproved($groupUser->group, $user, $approved));

            return back()->with('success', 'User "' . $user->name . '" was ' . ($approved ? 'approved' : 'rejected'));
        }

        return back();
    }

    public function removeUser(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You do not have permission to perform this action");
        }

        $data = $request->validate([
            'user_id' => ['required'],
        ]);

        $user_id = $data['user_id'];

        if ($group->isOwner($user_id)) {
            return response("The group owner cannot be removed", 403);
        }

        $groupUser =  GroupUser::where('user_id', $user_id)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $user = $groupUser->user;
            $groupUser->delete();
            
            $user->notify(new RemovedFromGroup($group));
        };


        return back();
    }

    public function changeRole(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You do not have permission to perform this action");
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'role' => ['required', Rule::enum(GroupUserRoleEnum::class)],
        ]);

        $user_id = $data['user_id'];

        if ($group->isOwner($user_id)) {
            return response("You cannot change the role of the group owner", 403);
        }

        $groupUser =  GroupUser::where('user_id', $user_id)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $groupUser->role = $data['role'];
            $groupUser->save();

            $groupUser->user->notify(new RoleChange($group, $data['role']));
        };


        return back();
    }
}
