<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $message;
    public int $groupId;

    public string $broadcastQueue = 'broadcast';

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param $message
     * @param $groupId
     * 
     * @return void
     */
    public function __construct(User $user, $message, $groupId)
    {
        $this->user = $user;
        $this->message = $message;
        $this->groupId = $groupId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // 公共频道用'Channel'，私有频道用 'PrivateChannel', 存在频道用 'PresenceChannel'
        return new PrivateChannel('wechat.group.' . $this->groupId);
    }
}
