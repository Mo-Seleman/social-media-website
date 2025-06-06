<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use DOMDocument;
use Inertia\Inertia;
use App\Notifications\PostCreated;
use App\Notifications\CommentCreated;
use App\Notifications\CommentDeleted;
use App\Notifications\ReactionAddedOnComment;
use App\Models\Comment;
use App\Models\Reaction;
use Illuminate\Http\Request;
use App\Models\PostAttachment;
use Illuminate\Validation\Rule;
use App\Http\Enums\ReactionEnum;
use App\Notifications\PostDeleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdatePostRequest;
use App\Notifications\ReactionAddedOnPost;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;

class PostController extends Controller
{

    public function view(Post $post)
    {
        $post->loadCount('reactions');
        $post->load([
            'comments' => function ($query) {
                $query->withCount('reactions');
            },
        ]);

        return Inertia::render('Post/View', [
            'post' => new PostResource($post),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $post = Post::create($data);

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];

            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id,
                ]);
            }

            DB::commit();

            $group = $post->group;

            if ($group) {
                $users = $group->approvedUsers()->where('user_id', '!=', $user->id)->get();
                Notification::send($users, new PostCreated($post, $user, $group));
            }

            $followers = $user->followers;
            Notification::send($followers, new PostCreated($post, $user, null));
        } catch (\Throwable $th) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $th;
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $data = $request->validated();
            $post->update($data);

            $deleted_ids = $data['deleted_files_ids'] ?? [];

            $attachments = PostAttachment::query()
                ->where('post_id', $post->id)
                ->whereIn('id', $deleted_ids)
                ->get();


            foreach ($attachments as $attachment) {
                $attachment->delete();
            }


            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];

            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id,
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $th;
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //TODO
        $id = Auth::id();

        if ($post->group && $post->group->isAdmin($id) || $post->isOwner($id)) {
            $post->delete();

            if (!$post->isOwner($id)) {
                $post->user->notify(new PostDeleted($post->group));
            }

            return back();
        }

        return response("You Do Not Have Permission To Delete This Post", 403);
    }

    public function downloadAttachment(PostAttachment $attachment)
    {
        // TODO check if user has permission to download
        return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
    }

    public function postReaction(Request $request, Post $post)
    {

        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)]
        ]);

        $userId = Auth::id();

        $reaction = Reaction::where('user_id', $userId)
            ->where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->first();

        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $post->id,
                'object_type' => Post::class,
                'user_id' => $userId,
                'type' => $data['reaction']
            ]);

            if (!$post->isOwner($userId)) {
                $user = Auth::user();
                $post->user->notify(new ReactionAddedOnPost($post, $user));
            }
        }

        $reactions = Reaction::where('object_id', $post->id)->where('object_type', Post::class)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction
        ]);
    }

    public function createComment(Request $request, Post $post)
    {
        $data = $request->validate([
            'comment' => ['required'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'comment' => nl2br($data['comment']),
            'user_id' => Auth::id(),
            'parent_id' => $data['parent_id'] ?: null,
        ]);

        $post = $comment->post;

        $commenter = Auth::user();

        $post->user->notify(new CommentCreated($comment, $commenter, $post));

        return response()->json(new CommentResource($comment), 201);
    }

    public function deleteComment(Comment $comment)
    {
        $post = $comment->post;
        $id = Auth::id();

        if ($comment->isOwner($id) || $post->isOwner($id)) {
            $comment->delete();

            if (!$comment->isOwner($id)) {
                $comment->user->notify(new CommentDeleted($comment, $post));
            }

            return response('', 204);
        }

        return response("You do not have permission to edit this post", 403);
    }

    public function updateComment(UpdateCommentRequest $request, Comment $comment)
    {
        $data = $request->validated();

        $comment->update([
            'comment' => nl2br($data['comment'])
        ]);

        return new CommentResource($comment);
    }

    public function commentReaction(Request $request, Comment $comment)
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)]
        ]);

        $userId = Auth::id();

        $reaction = Reaction::where('user_id', $userId)
            ->where('object_id', $comment->id)
            ->where('object_type', Comment::class)
            ->first();

        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $comment->id,
                'object_type' => Comment::class,
                'user_id' => $userId,
                'type' => $data['reaction']
            ]);

            if (!$comment->isOwner($userId)) {
                $user = Auth::user();
                $comment->user->notify(new ReactionAddedOnComment($comment->post, $comment, $user));
            }
        }

        $reactions = Reaction::where('object_id', $comment->id)->where('object_type', Comment::class)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction
        ]);
    }

    public function aiPostContent(Request $request)
    {
        $prompt = $request->get('prompt');

        $result = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Write a compelling social media post based on the following topic. Make the writing engaging and human-like.\n\nFormatting instructions:\n- Break the content into short paragraphs (2–4 sentences each).\n- Use line breaks between paragraphs.\n- If there are any hashtags, place them on a separate line at the bottom.\n\nTopic: " . $prompt
                ],
            ],
        ]);

        return response(['content' => $result->choices[0]->message->content]);
    }

    public function fetchUrlPreview(Request $request)
    {

        $validated = $request->validate([
            'url' => ['required', 'url'],
        ]);

        $url = $validated['url'];

        $html = file_get_contents($url);

        $dom = new DOMDocument();

        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        libxml_use_internal_errors(false);

        $ogTags = [];

        $metaTags = $dom->getElementsByTagName('meta');

        foreach ($metaTags as $tag) {
            $property = $tag->getAttribute('property');

            if (str_starts_with($property, 'og:')) {
                $ogTags[$property] = $tag->getAttribute('content');
            }
        }

        return $ogTags;
    }

    public function pinPostToggle(Request $request, Post $post)
    {

        $post->load('group');

        $forGroup = $request->get('forGroup', false);
        $group = $post->group;

        if ($forGroup && !$group) {
            return response("Invalid Request", 400);
        }

        if ($forGroup && !$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }

        $pinned = false;
        if ($forGroup && $group->isAdmin(Auth::id())) {
            if ($group->pinned_post_id === $post->id) {
                $group->pinned_post_id = null;
            } else {
                $pinned = true;
                $group->pinned_post_id = $post->id;
            }
            $group->save();
        }

        if (!$forGroup) {
            $user = $request->user();
            if ($user->pinned_post_id === $post->id) {
                $user->pinned_post_id = null;
            } else {
                $pinned = true;
                $user->pinned_post_id = $post->id;
            }

            $user->save();
        }

        return back()->with('success', 'Post was successfully ' . ($pinned ? 'pinned' : 'unpinned'));
    }
}
