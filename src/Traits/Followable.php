<?php

namespace Dealskoo\Follow\Traits;

use Dealskoo\Follow\Models\Follow;
use Illuminate\Database\Eloquent\Model;

trait Followable
{
    public function isFollowedBy(Model $model)
    {
        return $this->hasBeenFollowedBy($model);
    }

    public function hasFollower(Model $model)
    {
        return $this->hasBeenFollowedBy($model);
    }

    public function hasBeenFollowedBy(Model $model)
    {
        if (is_a($model, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('followers')) {
                return $this->followers->contains($model);
            }
            $follows = $this->relationLoaded('follows') ? $this->follows : $this->follows();
            return $follows->where('user_id', $model->getkey())->count() > 0;
        }
        return false;
    }

    public function follows()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function followers()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'follows', 'followable_id', 'user_id')->where('followable_type', $this->getMorphClass());
    }
}
