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


// test
Route::get('/test', function () {
    $key = "my_test:timer";
    dump($key);
    dump(htmlentities($key));
    $value = preg_replace('/&([a-z])[a-z]+;/i', '$1', htmlentities($key));
    dd($value);
})->middleware('throttle:10,1');
