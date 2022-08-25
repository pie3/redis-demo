<?php

namespace App\Events;

use App\Listeners\SwooleTestEventListener;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class SwooleTestEvent extends Event
{
    // 监听器列表
    protected $listeners = [
        SwooleTestEventListener::class,
    ];

    private $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
