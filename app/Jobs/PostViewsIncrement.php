<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PostViewsIncrement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Post $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * 两种并发频率限制:
         * 文档URL：https://laravelacademy.org/post/22188
         * 1、基于漏斗算法实现的并发请求频率限流器
         * 2、基于时间窗口实现的限流器
         */

        // 1、基于漏斗算法实现的并发请求频率限流器 - 限定最多支持 60 个并发处理进程
        Redis::funnel('post.views.increment')
            ->limit(60)
            ->then(function () {
                // 队列任务正常处理逻辑
                if ($this->post->increment('views')) {
                    // 将当前文章浏览数 +1，存储到对应 Sorted Set 的 Score 字段
                    Redis::zincrby('popular_posts', 1, $this->post->id);

                    // 更新 文章缓存值
                    $cacheKey = 'post_' . $this->post->id;
                    Cache::put($cacheKey, $this->post, 1 * 60 * 60); // 缓存 1 小时
                }
            }, function () {
                // 超出处理频率上限，延迟 60s 再执行
                $this->release(60);
            });

        /* // 2、基于时间窗口实现的限流器 - 每分钟（60s）最多执行 60 次
        Redis::throttle('post.views.increment')
        ->allow(60)->every(60)
        ->then(function(){
            // 队列任务正常处理逻辑
            if ($this->post->increment('views')) {
                // 将当前文章浏览数 +1，存储到对应 Sorted Set 的 Score 字段
                Redis::zincrby('popular_posts', 1, $this->post->id);

                // 更新 文章缓存值
                $cacheKey = 'post_' . $this->post->id;
                Cache::put($cacheKey, $this->post, 1 * 60 * 60); // 缓存 1 小时
            }
        }, function(){
            // 超出处理频率上限，延迟 60s 再执行
            $this->release(60);
        }); */
    }
}
