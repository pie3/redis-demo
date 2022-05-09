<?php

namespace App\Console\Commands;

use Illuminate\Cache\Lock;
use Illuminate\Cache\RedisLock;
use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection as RedisConnection;
use Illuminate\Support\Facades\Storage;

class ScheduleJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:job {process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mock Schedule Jobs';

    protected Lock $lock;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RedisConnection $redis)
    {
        parent::__construct();
        // 基于 redis 实现锁，过期时间 60s
        $this->lock = new RedisLock($redis, 'schedule_job', 60);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 尝试在给定的秒数（这里是 5 秒）内获取锁，如果获取到锁则执行回调函数,且执行完回调函数后释放锁
        $this->lock->block(5, function () {
            $processNo = $this->argument('process');
            for ($i = 1; $i <= 10; $i++) {
                $log = "Running Job #{$i} In Process #{$processNo}";
                // 将运行日志记录到本地文件存储（storage/app/schedule_jog_logs）
                Storage::disk('local')->append('schedule_job_logs', $log);
            }
        });
    }
}
