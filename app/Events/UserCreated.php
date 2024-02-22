<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\User;

class UserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $generate_password;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $generate_password = false)
    {
        $this->user  = $user;
        $this->generate_password  = $generate_password;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
