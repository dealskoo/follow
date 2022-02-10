<?php

namespace Dealskoo\Follow\Tests;

use Dealskoo\Follow\Traits\Followable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Followable;

    protected $fillable = ['name'];
}
