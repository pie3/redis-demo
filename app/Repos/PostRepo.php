<?php

namespace App\Repos;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PostRepo
{
    protected $trendingPostsKey = 'popular_posts';
    protected $postKeyPrefix = 'post_';
    protected Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getById(int $id, array $columns = ['*'])
    {
        $cacheKey = $this->postKeyPrefix . $id;
        // 缓存 1 小时
        return Cache::remember($cacheKey, 1 * 60 * 60, function () use ($id, $columns) {
            return $this->post->select($columns)->find($id);
        });
    }

    public function getByManyId(array $ids, array $columns = ['*'], callable $callback = null)
    {
        $query = $this->post->select($columns)->whereIn('id', $ids);
        if ($callback) {
            $query = $callback($query);
        }

        return $query->get();
    }

    public function addVidws(Post $post)
    {
        $post->increment('views');
        if ($post->save()) {
            // 将当前文章浏览数 +1，存储到对应 Sorted Set 的 Score 字段
            Redis::zincrby($this->trendingPostsKey, 1, $post->id);

            // $post->refresh(); // refresh 方法会使用数据库中的新数据重新赋值现有的模型。

            // 更新 文章缓存值
            $cacheKey = $this->postKeyPrefix . $post->id;
            Cache::put($cacheKey, $post, 1 * 60 * 60); // 缓存 1 小时

        }

        return $post->views;
    }

    public function trending($num = 10)
    {
        $cacheKey = $this->trendingPostsKey . '_' .  $num;
        // 缓存 10 分钟
        return Cache::remember($cacheKey, 10 * 60, function () use ($num) {
            $postIds = Redis::zrevrange($this->trendingPostsKey, 0, $num - 1);
            if ($postIds) {
                $idStr = implode(',', $postIds);
                return $this->getByManyId($postIds, ['*'], function ($query) use ($idStr) {
                    return $query->orderByRaw('field(`id`, ' . $idStr . ')');
                });
            }
        });
    }
}
