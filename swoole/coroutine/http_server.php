<?php
$server = new \Swoole\Http\Server('127.0.0.1', 9588);

$server->on('request', function ($request, $response) {
    var_dump(time());

    $mysql = new \Swoole\Coroutine\Mysql();
    $mysql->connect([
        'host' => 'mysql', // 用的是 .env 里的配置值
        'user' => 'root',
        'password' => 'root',
        'database' => 'redis_demo',
    ]);
    $mysql->setDefer();
    $mysql->query('select sleep(3)');

    var_dump(time());

    $redis1 = new \Swoole\Coroutine\Redis();
    $redis1->connect('redis', 6379); // 用的是 .env 里的配置值
    $redis1->setDefer(1);
    $redis1->set('hello', 'world');

    var_dump(time());

    $redis2 = new \Swoole\Coroutine\Redis();
    $redis2->connect('redis', 6379); // 用的是 .env 里的配置值
    $redis2->setDefer(1);
    $redis2->get('hello');

    $result1 = $mysql->recv();
    $result2 = $redis2->recv();

    var_dump($result1, $result2, time());

    $response->end('Request Finish: ' . time());
});

$server->start();
