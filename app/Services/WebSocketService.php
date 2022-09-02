<?php

namespace App\Services;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

class WebSocketService implements WebSocketHandlerInterface
{
    /**
     * @var \Swoole\Table $wsTable
     */
    private $wsTable;

    public function __construct()
    {
        $this->wsTable = app('swoole')->wsTable;
    }

    // 连接建立时触发
    public function onOpen(Server $server, Request $request)
    {
        // 在触发 WebSocket 连接建立事件之前，Laravel 应用初始化的生命周期已经结束，你可以在这里获取 Laravel 请求和会话数据
        // 调用 push 方法向客户端推送数据，fd 是客户端连接标识字段
        Log::info('WebSocket 连接建立:' . $request->fd);
        // 通过 swoole 实例上的 wsTable 属性(在 config/larabels.php 中的 'swoole_tables' 项配置)，访问 Swoole Table
        $this->wsTable->set('fd:' . $request->fd, ['fd' => $request->fd]);
        $server->push($request->fd, "Welcome to WebSocket Server built on LaravelS");
    }

    // 收到消息时触发
    public function onMessage(Server $server, Frame $frame)
    {
        foreach ($this->wsTable as $key => $row) {
            if (strpos($key, 'fd:') === 0 && $server->exist($row['fd'])) {
                Log::info('Receive message from client: ' . $row['fd']);
                // 调用 push 方法向客户端推送数据
                $server->push($frame->fd, 'This is a message sent from WebSocket Server at ' . date('Y-m-d H:i:s'));
            }
        }
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('WebSocket 连接关闭');
    }
}
