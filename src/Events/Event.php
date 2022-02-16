<?php

namespace Dealskoo\Follow\Events;

use Dealskoo\Follow\Models\Follow;

class Event
{
    public $follow;

    public function __construct(Follow $follow)
    {
        $this->follow = $follow;
    }
}
