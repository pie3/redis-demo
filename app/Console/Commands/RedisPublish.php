<?php

namespace App\Console\Commands;

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
        
        $data = [
            'event' => 'UserSignedUp',
            'data' => [
                'username' => 'Python'
            ]
        ];

        Redis::publish('test-channel', json_encode($data));
   

        /*
        $user = User::find(1);
        event(new UserSignedUp($user));
        */

        /* $user = User::find(1);
        $message = 'hello, Pie!';
        $groupId = 1;
        event(new UserSendMessage($user, $message, $groupId)); */
    }
}
