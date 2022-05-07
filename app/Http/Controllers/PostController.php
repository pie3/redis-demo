<?php

namespace App\Http\Controllers;

use App\Jobs\PostViewsIncrement;
use App\Repos\PostRepo;

class PostController extends Controller
{
    protected PostRepo $postRepo;

    public function __construct(PostRepo $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    // 文章浏览
    public function show($id)
    {
        $post = $this->postRepo->getById($id);
        // 分发队列任务
        $this->dispatch(new PostViewsIncrement($post));
        // $views = $this->postRepo->addVidws($post);

        return "Show Post #{$post->id}, Views: {$post->views}";
    }

    // 获取热门文章排行榜
    public function popular()
    {
        // 获取浏览最多的前十篇文章
        $posts = $this->postRepo->trending(10);
        if($posts){
            dd($posts->toArray());
        }

    }
}
