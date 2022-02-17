<?php

namespace Dealskoo\Follow\Events;

use Dealskoo\Follow\Models\Follow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $follow;

    public function __construct(Follow $follow)
    {
        $this->follow = $follow;
    }
}
