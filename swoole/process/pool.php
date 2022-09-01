<?php
echo "Master pid=" . posix_getpid() . "\n";
$workerNum = 5;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started, pid=" . posix_getpid() .  "\n";
    $redis = new Redis();
    $redis->pconnect('redis', 6379); // redis 为本地 laradock 开发环境中的 redis service
    $key = "key1";
    while (true) {
        $msgs = $redis->brPop($key, 2);
        if ($msgs == null) {
            continue;
        }
        var_dump($msgs);
        echo "Processed by Worker#{$workerId}, pid=" . posix_getpid() . "\n\n";
    }
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();
