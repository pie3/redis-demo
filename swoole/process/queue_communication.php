<?php

use Swoole\Process;

$process = new Process(function (Process $worker) {
    // 子进程逻辑
    // 从消息队列读取数据
    $cmd = $worker->pop();
    echo "Message from master process: " . $cmd . "\n";
    ob_start();
    // 执行外部程序并显示未经处理的原始输出，会直接打印输出
    passthru($cmd);
    $ret = ob_get_clean() ?: ' ';
    $ret = trim($ret) . ". worker pid:" . $worker->pid . "\n";
    // 将返回消息推送到消息队列
    $worker->push($ret);
    $worker->exit(0); // 退出子进程
}, false, false); // 关闭管道

// 调用 useQueue 表示使用 消息队列 进行 进程间通信
// 消息队列 与 管道通信 不能共存
// 第一个参数表示 消息队列 里的 key，第二个参数表示 通信模式， 2 表示 争抢模式
// 使用 争抢模式 进行通信时，哪个 子进程 先读取到消息先消费，因此无法实现与指定 子进程 的通信
// 消息队列 不支持 事件循环， 因此引入了 \Swoole\Process::IPC_NOWAIT 表示以 非阻塞模式 进行通信
$process->useQueue(1, 2 | \Swoole\Process::IPC_NOWAIT);
// 从 主进程 将命令推送到 消息队列
$process->push('php --version');
// 从 消息队列 读取返回消息
$msg = $process->pop();
echo "Message from worker process: " . $msg;

// 启动子进程
$process->start();

Process::wait(); // 要调用这段代码，否则 子进程 中的 push 或 pop 可能会报错
