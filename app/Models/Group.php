<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Auth;
use App\Http\Enums\GroupUserRoleEnum;
use App\Http\Enums\GroupUserStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;

    protected $fillable = ['name', 'user_id', 'auto_approval', 'about', 'cover_path', 'thumbnail_path'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
        ->generateSlugsFrom('name')
        ->saveSlugsTo('slug')
        ->doNotGenerateSlugsOnUpdate();
    }

    public function currentUserGroup(): HasOne
    {
        return $this->hasOne(GroupUser::class)->where('user_id', Auth::id());
    }

    public function isAdmin($userId): bool
    {
        return GroupUser::query()
            ->where('user_id', $userId)
            ->where('group_id', $this->id)
            ->where('role', GroupUserRoleEnum::ADMIN->value)
            ->exists();
    }

    public function isOwner($userId): bool
    {
        return $this->user_id == $userId;
    }

    public function adminUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users')->wherePivot('role', GroupUserRoleEnum::ADMIN->value);
    }
    public function pendingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users')->wherePivot('status', GroupUserStatusEnum::PENDING->value);
    }
    public function approvedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users')->wherePivot('status', GroupUserStatusEnum::APPROVED->value);
    }
}
