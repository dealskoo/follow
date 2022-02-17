<?php

namespace Dealskoo\Follow\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Unfollowed extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
