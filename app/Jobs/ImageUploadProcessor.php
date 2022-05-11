<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * 此任务创建文档来源：https://laravelacademy.org/post/22198
 */

class ImageUploadProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 文件名
    public string $name;
    // 文件内容
    public string $content;
    // 所属文章
    public Post $post;

    // 最大尝试次数，超过标记为执行失败
    public int $tries = 10;
    // 最大异常数，超过标记为执行失败
    public int $maxExceptions = 3;
    // 超时时间，3 分钟，超过则标记为执行失败
    public int $timeout = 180;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $name, string $content, Post $post)
    {
        $this->name = $name;
        $this->content = $content;
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = 'images/' . $this->name;

        // 如果文件已存在，则退出
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        // 文件存储成功，则将其保存到数据库，否则 5s 后重试
        if (Storage::disk('public')->put($path, base64_decode($this->content))) {
            $image = new Image();
            $image->name = $this->name;
            $image->path = $path;
            // 为了后面 web URL 可以访问，需要执行：php artisan storage:link 命令创建对应的软连接，
            // php artisan storage:link 命令会根据配置文件 config/filesystems.php 中的 'links' 配置项来创建对应的软连接
            $image->url = config('app.url') . '/storage/' . $path;
            $image->user_id = $this->post->user_id;

            if ($image->save()) {
                // 图片保存成功，则更新 posts 表的 image_id 字段
                $this->post->image_id = $image->id;
                $image->posts->save($this->post);
            } else {
                // 图片保存失败，则删除当前图片，并在 5s 后重试此任务
                Storage::disk('public')->delete($path);
                $this->release(5);
            }

            // 如果有缩略图、裁剪等后续处理，可以在这里执行

        } else {
            // 文件存储不成功，5s 后重试此存储任务
            $this->release(5);
        }
    }
}
