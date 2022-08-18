<?php

namespace Swoole;


// Swoole 4 以后通过协程来实现异步通信
go(function () {
    $client = new Coroutine\Client(SWOOLE_SOCK_TCP);
    // 尝试与指定 TCP 服务端建立连接（IP 和端口号需要与服务端保持一致，超时时间为 0.5 秒）
    if ($client->connect("127.0.0.1", 9503, 0.5)) {
        // 建立连接后发送内容
        $client->send("Hello World!\n");
        // 打印接收到的消息
        echo $client->recv();
        // 关闭连接
        $client->close();
    } else {
        echo "connect failed.";
    }
});
