<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $comments = $this->comments;

        return [
            'id' => $this->id,
            'body' => $this->body,
            'preview' => $this->preview,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
            'user' => new UserResource($this->user),
            'group' => new GroupResource($this->group),
            'attachments' => PostAttachmentResource::collection($this->attachments),
            'num_of_reactions' => $this->reactions_count,
            'num_of_comments' => count($comments),
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'comments' => self::convertCommentsIntoTree($comments),
        ];
    }

    private static function convertCommentsIntoTree($comments, $parentId = null): array
    {
            $commentTree = [];

            foreach ($comments as $comment) {
                if ($comment->parent_id === $parentId) {
                    $children = self::convertCommentsIntoTree($comments, $comment->id);
                    $comment->childComments = $children;
                    $comment->numOfComments = collect($children)->sum('numOfComments') + count($children);
                    $commentTree[] = new CommentResource($comment);
            }
        }

        return $commentTree;
    }
}