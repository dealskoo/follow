<?php

namespace Dealskoo\Follow\Events;

use Illuminate\Database\Eloquent\Model;

class Event
{
    public $follow;

    public function __construct(Model $follow)
    {
        $this->follow = $follow;
    }
}
