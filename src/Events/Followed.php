<?php

namespace Dealskoo\Follow\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Followed extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
