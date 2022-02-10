<?php

namespace Dealskoo\Follow\Tests;

use Dealskoo\Follow\Traits\Followable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Followable;

    protected $fillable = ['title'];
}
