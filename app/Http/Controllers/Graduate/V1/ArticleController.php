<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Validator;
use Lib\Fdfs\Lm;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ArticleController extends AuthController
{
    // 主表类名
    public $namespace = Article::class;

    /**
     * 图片上传
     *
     * @param Request $request
     * @return array
     * @author long
     */
    public function cover(Request $request)
    {
        $lm = new Lm();
        $tmp = $request->input('cover');
        $path = $this->base64ToPic($tmp);
        if ($path) {
            $file = $lm->up($path);
            unlink($path); // 删除文件
            if (!$file) {
                return $this->error('FastDfs挂了，上传封面失败！');
            }
            return $this->success($file);
        } else {
            return $this->error('前端上传问题，封面上传失败！');
        }
    }



    /**
     * 上传文中图片
     *
     * @param Request $request
     * @return void
     * @author chendaye
     */
    public function markDownPic(Request $request)
    {
        $lm = new Lm();
        $tmp = $_FILES['file'];
        if (!$tmp) {
            return $this->error('markdown上传有误');
        }

        $file = $lm->up((string) $tmp['tmp_name']);
        unlink($tmp['tmp_name']); // 删除文件
        if (!$file) {
            return $this->error('FastDfs挂了，插图上传失败！');
        }
        return $this->response->array($file);
    }

    /**
     * base64 生成图片
     *
     * @param [type] $data
     * @return string
     * @author chendaye
     */
    public function base64ToPic($data)
    {
        // 取base64 编码  cover: data:image/png;base64,iVBORw...
        if (strstr($data, ",")) {
            $image = explode(',', $data);
            $image = $image[1];
        }

        // 获取图片后缀
        if (strstr($data, ";")) {
            $ext = explode(';', $data);
            $ext = explode('/', $ext[0]);
            $ext = $ext[1];
        }

        $imageName = date("His", time()) . "_" . time() . '.' . $ext;

        $imageSrc = "/tmp/" . $imageName; //图片名字
        $r = file_put_contents($imageSrc, base64_decode($image)); //返回的是字节数
        if (!$r) {
            return false;
        } else {
            return $imageSrc;
        }
    }

    /**
     * 创建笔记
     *
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function article(Request $request)
    {
        $data = $this->json($request->all());
        // 文章内容
        $article = [
            'title' => $data['title'],
            'abstract' => $data['abstract'],
            'cover' => $data['cover']['sortUrl'],
            'content' => $data['markdown'],
            'html' => $data['html'],
            'draft' => $data['draft'],
            'user_id' => Auth::guard('api')->id()
        ];
        // 保存文章
        if ($this->model->where('title', '=', $article['title'])->exists()) return $this->error('文章标题重复！');
        // 不存在就插入
        $articleInfo = $this->model->add($article);
        if (!$articleInfo) return $this->error('文章保存失败！');
        $articleId = $articleInfo['id'];
        // 保存文章标签
        $articleModel = Article::find($articleId); // 获取要关联的文章
        foreach ($data['tags'] as $val) {
            try {
                $articleModel->tags()->attach($val);
            } catch (\Expectation $e) {
                return $this->error($e->getMessage());
            }
        }
        // 保存文章分类
        foreach ($data['category'] as $val) {
            try {
                $articleModel->categorys()->attach($val);
            } catch (\Expectation $e) {
                return $this->error($e->getMessage());
            }
        }
        // 返回文章信息
        return $this->success($articleInfo);
    }

    /**
     * 检查标题是否重复
     */
    public function checkTitle(Request $request)
    {
        $title = $request->input('title');
        if ($this->model->where('title', '=', $title)->exists()) {
            return $this->success(true);
        } else {
            return $this->success(false);
        }
    }
}
