<?php

namespace App\Jobs;

use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * reference : https://github.com/hhxsv5/laravel-s/blob/master/README-CN.md#异步的任务队列
 */
class SwooleAsyncTestTask extends Task
{
    // 待处理数据
    private $data;
    // 任务处理结果
    private $result;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    // 任务投递调用 task 回调时触发，等同于 Swoole 中的 onTask 逻辑
    // 运行在 Task 进程中，不能投递任务
    public function handle()
    {
        Log::info(__CLASS__ . ': 开始处理任务', [$this->data]);
        // todo 耗时任务具体处理逻辑在这里编写
        sleep(3); // 模拟任务需要 3 秒才能执行完毕
        $this->result = 'The result of ' . $this->data . ' is balabalabala';
    }

    // 任务完成调用 finish 回调时触发，等同于 Swoole 中的 onFinish 逻辑
    // 可选的，完成事件，任务处理完成后的逻辑，运行在 worker 进程中，可以投递任务
    public function finish()
    {
        Log::info(__CLASS__ . ': 任务处理完成', [$this->result]);
        // 可以在这里触发后续要执行的任务，或者执行其他善后逻辑,如下：
        // Task::deliver(new TeskTask('task data')); // 投递其他任务
    }
}
