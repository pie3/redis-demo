<?php

// namespace Swoole;

// 初始化 Websocket 服务器，在本地监听 8000 端口
$server = new Swoole\WebSocket\Server("localhost", 8000);

// 建立连接时触发
$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

// 接收消息时触发推送
$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "this is server");
});

// 关闭 Websocket 连接时触发
$server->on('close', function ($server, $fd) {
    echo "Client {$fd} Closed.\n";
});

// 向服务器发送请求时返回响应
$server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
    global $server; // 调用外部的 server
    // $server->connections // 遍历所有 websocket 连接用户的 fd，给所有用户推送
    foreach ($server->connections as $fd) {
        // 需要先判断是否是正确的 websocket 连接，否则有可能会 push 失败
        if ($server->isEstablished($fd)) {
            $server->push($fd, $request->get['message']);
        }
    }
});

// 启动 Websocket 服务器
$server->start();
