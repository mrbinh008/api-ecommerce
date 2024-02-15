<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestMail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $email;
    public string $name;

    /**
     * Create a new event instance.
     */
    public function __construct($email, $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('test-mail'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, string>
     */
    public function broadcastWith(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name
        ];
    }
}
