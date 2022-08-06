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

class UserEnterGroup implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public int $groupId;
    public string $broadcastQueue = 'broadcast';

    /**
     * Create a new event instance.
     *
     * @param  User $user
     * @param int $groupId
     */
    public function __construct(User $user, $groupId)
    {
        $this->user = $user;
        $this->groupId = $groupId;
        // $this->dontBroadcastToCurrentUser(); // 将这个事件消息广播给排除当前用户的所有其他在线用户
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // 公共频道用'Channel'，私有频道用 'PrivateChannel', 存在频道用 'PresenceChannel'
        return new PresenceChannel('wechat.group.' . $this->groupId);
    }
}
