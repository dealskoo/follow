<?php

namespace Dealskoo\Follow\Traits;

use Dealskoo\Follow\Models\Follow;
use Illuminate\Database\Eloquent\Model;

trait Follower
{
    public function follow(Model $model)
    {
        if (!$this->hasFollowed($model)) {
            $follow = new Follow();
            $follow->user_id = $this->getKey();
            $model->follows()->save($follow);
        }
    }

    public function unfollow(Model $model)
    {
        $follow = Follow::query()
            ->where('followable_id', $model->getKey())
            ->where('followable_type', $model->getMorphClass())
            ->where('user_id', $this->getKey())
            ->first();
        if ($follow) {
            if ($this->relationLoaded('follows')) {
                $this->unsetRelation('follows');
            }
            return $follow->delete();
        }
        return true;
    }

    public function toggleFollow(Model $model)
    {
        return $this->hasFollowed($model) ? $this->unfollow($model) : $this->follow($model);
    }

    public function hasFollowed(Model $model)
    {
        $follows = $this->relationLoaded('follows') ? $this->follows : $this->follows();
        return $follows->where('followable_id', $model->getKey())->where('followable_type', $model->getMorphClass())->count() > 0;
    }

    public function follows()
    {
        return $this->hasMany(Follow::class, 'user_id', $this->getKeyName());
    }

    public function getFollowedItems(string $model)
    {
        return app($model)->whereHas('follows', function ($q) {
            return $q->where('user_id', $this->getKey());
        });
    }
}
