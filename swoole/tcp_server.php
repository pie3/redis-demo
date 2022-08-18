<?php

namespace Swoole;

// 监听本地 9503 端口，等待客户端请求
$server = new Server('127.0.0.1', 9503);

// 建立连接时输出
$server->on("connect", function ($serv, $fd) {
    echo "Client:Connect, fd={$fd}.\n";
});

// 接收消息时返回
$server->on("receive", function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole TCP Server Response: ' . $data);
    $serv->close($fd);
});

$server->on("close", function ($serv, $fd) {
    echo "Client: Close.\n";
});

// 启动 TCP 服务
$server->start();
