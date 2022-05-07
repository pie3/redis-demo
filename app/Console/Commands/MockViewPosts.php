<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class MockViewPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mock:view-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mock View Posts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 1、先清空 posts 表
        Post::truncate();
        // 2、删除对应的 热门文章 的 Redis 键
        Redis::del('popular_posts');
        // 3、生成 100 篇测试文章
        Post::factory()->count(100)->create();
        // 4、模拟对所有文章进行 1000 次随机访问
        for ($i = 0; $i < 1000; $i++) {
            $postId = mt_rand(1, 100);
            $response = Http::get('http://redis-demo.test/posts/' . $postId);
            $this->info($response->body());
        }
    }
}
