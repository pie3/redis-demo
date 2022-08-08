<?php

namespace App\Console\Commands;

use App\Events\UserEnterGroup;
use App\Events\UserSendMessage;
use App\Events\UserSignedUp;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redis Publish Message';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 以数组形式模拟的事件消息数据
        /* $data = [
            'event' => 'UserSignedUp',
            'data' => [
                'username' => 'Python'
            ]
        ];

        Redis::publish('test-channel', json_encode($data));
         */

        // 分发广播事件 - UserSignedUp - 公共频道事件
        /* $user = User::find(1);
        event(new UserSignedUp($user));
        */

        // 分发广播事件 - UserSendMessage - 私有频道事件
        $user = User::find(1);
        $message = 'Hello, Pie!';
        $groupId = 1;
        event(new UserSendMessage($user, $message, $groupId));


        // 分发广播事件 - UserSendMessage - 存在频道事件
        /* $user = User::find(1);
        $groupId = 1;
        event(new UserEnterGroup($user, $groupId));
         */
    }
}
