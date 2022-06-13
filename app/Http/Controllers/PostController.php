<?php

namespace App\Http\Controllers;

use App\Jobs\ImageUploadProcessor;
use App\Jobs\PostViewsIncrement;
use App\Models\Post;
use App\Repos\PostRepo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostRepo $postRepo;

    public function __construct(PostRepo $postRepo)
    {
        $this->postRepo = $postRepo;
        // 需要登录认证后才能发布文章
        $this->middleware('auth')->only(['create', 'store']);
    }

    // 文章浏览
    public function show($id)
    {
        $post = $this->postRepo->getById($id);
        // 分发队列任务
        $this->dispatch(new PostViewsIncrement($post));
        // $views = $this->postRepo->addVidws($post);

        // return "Show Post #{$post->id}, Views: {$post->views}";
        return view('posts.show', ['post' => $post]);
    }

    // 文章发布页面
    public function create()
    {
        return view('posts.create');
    }

    // 文章发布处理
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string|min:10',
            'image' => 'required|image|max:1024' // 尺寸不能超过 1 MB
        ]);

        $post = new Post($data);
        $post->user_id = $request->user()->id;
        try {
            if ($post->save()) {
                $image = $request->file('image');
                // 获取图片名称
                $name = $image->getClientOriginalName();
                // 获取图片二进制数据后通过 Base64 进行编码
                // $content = base64_encode($image->getContent());
                // 获取图片存储的临时路径（相对路径）
                $path = $image->store('temp');
                // 通过图片处理任务类将图片存储工作推送到 uploads 队列进行异步处理, 下面有三种推送方法
                ImageUploadProcessor::dispatch($name, $path, $post)->onQueue('uploads');  // 1、直接通过任务类的 dispatch 方法 推送任务
                // $this->dispatch(new ImageUploadProcessor($name, $content, $post))->onQueue('uploads'); // 2、通过 trait DispatchesJobs 的 dispatch 方法 推送任务
                // dispatch(new ImageUploadProcessor($name, $content, $post))->onQueue('uploads'); // 3、通过辅助函数 dispatch 推送任务

                return redirect('posts/' . $post->id);
            }

            return back()->withInput()->with(['status' => '文章发布失败，请重试']);
        } catch (QueryException $exception) {
            return back()->withInput()->with(['status' => '文章发布失败，请重试']);
        }
    }


    // 获取热门文章排行榜
    public function popular()
    {
        // 获取浏览最多的前十篇文章
        $posts = $this->postRepo->trending(10);
        if ($posts) {
            dd($posts->toArray());
        }
    }
}
