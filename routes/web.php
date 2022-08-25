<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';


// 打印 redis connection info
Route::get('/redis-connection', function () {
    dd(\Illuminate\Support\Facades\Redis::connection());
    // 或者
    // dd(app('redis')->connection());
    // 又或者
    // dd(app('redis.connection'));
});

// 获取网站全局访问量
Route::get('/site-visits', function () {
    return '网站全局访问量：' . \Illuminate\Support\Facades\Redis::get('site_total_visits');
});

// 获取热门文章排行榜
Route::get('/posts/popular', [PostController::class, 'popular']);
// 获取文章详情
Route::get('/posts/{id}', [PostController::class, 'show'])->where('id', '[0-9]+');
// 显示创建文章页面
Route::get('/posts/create', [PostController::class, 'create']);
// 保存创建文章内容
Route::post('/posts/store', [PostController::class, 'store']);

// 广播路由（基于 Redis 发布订阅（Redis::publish + Redis::subscribe） + socket.io（ioredis + http + socket.io-client））
// 文档来源：https://laravelacademy.org/post/22179
Route::get('/broadcast-sio', function () {
    return view('websocket');
});

// 广播路由（基于 Redis 实现 Laravel 广播功能（Redis::publish + Redis::subscribe） + Laravel 广播组件 + Laravel Echo Server + Laravel Echo）
// 文档来源：https://laravelacademy.org/post/22180 22181
// https://laravelacademy.org/post/22181
// 注意：项目 package.json 中已安装的 socket.io-client 版本需调整为与 laravel-echo-server 中的 socket.io 版本一致，否则很可能导致
// Websocket 连接建立失败
//（查看 laravel-echo-server 中的 socket.io 版本的方法如下:
// [或者更简单的： docker-compose exec laravel-echo-server  npm explain socket.io ]
// 1、进入 laravel-echo-server 容器：docker-compose exec --user=root laravel-echo-server sh
// 2-1、方法1：npm explain socket.io
// 2-2、方法2: 查看 laravel-echo-server 容器中 socket.io 的 package.json 文件：cat node_modules/socket.io/package.json
// ）
Route::get('/broadcast', function () {
    return view('websocket');
});

// 广播路由 - 基于 Redis 实现 Laravel 广播功能 - 在私有频道发布和接收消息
Route::get('/broadcast-private', function () {
    return view('websocket');
});

// 广播路由 - 基于 Redis 实现 Laravel 广播功能 - 在存在频道发布和接收消息
Route::get('/broadcast-presence', function () {
    return view('websocket');
});

// 广播路由 - 基于 Redis 实现 Laravel 广播功能 - 通过路由分发广播事件 - 推送广播消息给其他用户
// 文档来源：https://laravelacademy.org/post/22182
Route::post('/groups/{id}/enter', function ($id) {
    broadcast(new \App\Events\UserEnterGroup(request()->user(), $id))->toOthers();
    return true;
});


// Websocket 客户端路由
Route::get('/wsc', function () {
    return view('swoole.websocket-client');
});

// Swoole 异步任务测试
Route::get('/task/test-async', function () {
    $task = new \App\Jobs\SwooleAsyncTestTask('测试异步任务');
    $success = \Hhxsv5\LaravelS\Swoole\Task\Task::deliver($task); // 异步投递任务，触发调用任务类的 handle 方法
    var_dump($success);
});

// Swoole 自定义异步事件监听及处理 路由
Route::get('/event/test', function () {
    $event = new \App\Events\SwooleTestEvent('测试 Swoole 异步事件监听及处理');
    $success = \Hhxsv5\LaravelS\Swoole\Task\Event::fire($event);
    var_dump($success);
});


// test
Route::get('/test', function () {
    $key = "my_test:timer";
    dump($key);
    dump(htmlentities($key));
    $value = preg_replace('/&([a-z])[a-z]+;/i', '$1', htmlentities($key));
    dd($value);
})->middleware('throttle:10,1');
