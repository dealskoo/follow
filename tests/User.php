<?php

namespace Dealskoo\Follow\Tests;

use Dealskoo\Follow\Traits\Follower;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Follower;
    protected $fillable = ['name'];
}
