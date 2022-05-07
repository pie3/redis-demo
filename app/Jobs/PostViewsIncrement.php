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
        if ($this->post->increment('views')) {
            // 将当前文章浏览数 +1，存储到对应 Sorted Set 的 Score 字段
            Redis::zincrby('popular_posts', 1, $this->post->id);

            // 更新 文章缓存值
            $cacheKey = 'post_' . $this->post->id;
            Cache::put($cacheKey, $this->post, 1 * 60 * 60); // 缓存 1 小时
        }
    }
}
