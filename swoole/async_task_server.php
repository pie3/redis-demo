<?php

$server = new \Swoole\Server("127.0.0.1", 9503);

// 设置异步任务的工作进程数量
$server->set(array('task_worker_num' => 4));

// 收到请求时触发
$server->on("receive", function (\Swoole\Server $server, $fd, $from_id, $data) {
    // 投递异步任务
    $task_id = $server->task($data);
    echo "异步任务投递成功：id=$task_id\n";
    $server->send($fd, "数据已接收，处理中...\n");
});

// 处理异步任务
$server->on("task", function (\Swoole\Server $server, $task_id, $from_id, $data) {
    echo "新的待处理异步任务[id=$task_id]" . PHP_EOL;
    // todo 处理异步任务
    // 返回任务执行的结果
    $server->finish("$data -> OK");
});

// 处理异步任务的结果
$server->on("finish", function (\Swoole\Server $server, $task_id, $data) {
    echo "异步任务[$task_id]处理完成：$data" . PHP_EOL;
});

$server->start();
