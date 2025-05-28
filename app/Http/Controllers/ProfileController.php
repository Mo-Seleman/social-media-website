<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Follower;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\PostResource;
use App\Models\PostAttachment;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{

    public function index(Request $request, User $user)
    {
        $isCurrentUserFollower = false;

        if (!Auth::guest()) {
            $isCurrentUserFollower = Follower::where('user_id', $user->id)->where('follower_id', Auth::id())->exists();
        }

        $followerCount =  Follower::where('user_id', $user->id)->count();

        $posts = Post::postsForTimeline(Auth::id(), false)
            ->leftJoin('users AS u', 'u.pinned_post_id', 'posts.id')
            ->where('user_id', $user->id)
            ->orderBy('u.pinned_post_id', 'desc')
            ->orderBy('posts.created_at', 'desc')
            ->paginate(5);

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        $followers = $user->followers;

        $following = $user->following;

        $photos = PostAttachment::query()
            ->where('mime', 'like', 'image/%')
            ->Where('created_by', $user->id)
            ->get();

        $following = User::query()
            ->select('users.*')
            ->join('followers AS f', 'f.user_id', 'users.id')
            ->where('f.follower_id', $user->id)
            ->latest()
            ->get();

        return Inertia::render('Profile/View', [

            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'success' => session('success'),
            'isCurrentUserFollower' => $isCurrentUserFollower,
            'followerCount' => $followerCount,
            'user' => new UserResource($user),
            'posts' => $posts,
            'followers' => UserResource::collection($followers),
            'following' => UserResource::collection($following),
            'photos' => PostAttachmentResource::collection($photos),
        ]);
    }


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile', $request->user())->with('success', 'Profile Details Successfully Updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateImage(Request $request)
    {
        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'avatar' => ['nullable', 'image']
        ]);

        $user = $request->user();

        $avatar = $data['avatar'] ?? null;
        // @var \Illuminate\Http\UploadedFile $cover
        $cover = $data['cover'] ?? null;

        $success = '';

        if ($cover) {

            if ($user->cover_path) {
                Storage::disk('public')->delete($user->cover_path);
            }

            $path = $cover->store('user-' . $user->id, 'public');
            $user->update(['cover_path' => $path]);
            $success = 'Your Cover Image Has Succcessfully Been Updated';
        }

        if ($avatar) {

            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $avatar->store('user-' . $user->id, 'public');
            $user->update(['avatar_path' => $path]);
            $success = 'Your Avatar Image Has Succcessfully Been Updated';
        }

        return back()->with('success', $success);
    }
}
