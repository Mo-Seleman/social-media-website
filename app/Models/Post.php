<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'body', 'group_id', 'preview'];

    protected $casts = [
        'preview' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class)->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function latest5Comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'object');
    }

    public static function postsForTimeline($userId, $getLatest = true): Builder
    {
        $query = Post::query()
            ->withCount('reactions')
            ->with([
                'user',
                'group',
                'group.currentUserGroup',
                'attachments',
                'comments' => function ($query) {
                    $query
                        ->withCount('reactions');
                },
                'comments.user',
                'comments.reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ]);

        if ($getLatest) {
            $query->latest();
        }

        return $query;
    }

    public function isOwner($userId)
    {
        return $this->user_id == $userId;
    }
}
