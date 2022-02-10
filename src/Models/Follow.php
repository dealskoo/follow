<?php

namespace Dealskoo\Follow\Models;

use Dealskoo\Follow\Events\Followed;
use Dealskoo\Follow\Events\Unfollowed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => Followed::class,
        'deleted' => Unfollowed::class
    ];

    public function followable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function follower()
    {
        return $this->user();
    }

    public function scopeWithType(Builder $builder, string $type)
    {
        return $builder->where('followable_type', app($type)->getMorphClass());
    }
}
