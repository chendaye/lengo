<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Lib\Fdfs\Lm;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Lib\Redis\Rds;

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
        $user =  Auth::guard('api')->user() ?? Auth::guard('client')->user();
        $article = [
            'title' => $data['title'],
            'abstract' => $data['abstract'],
            'cover' => $data['cover']['sortUrl'],
            'content' => $data['markdown'],
            'html' => $data['html'],
            'draft' => $data['draft'],
            'user_id' => $user->id,
            'user_name' => $user->name
        ];
        // 保存文章
        if ($this->model->where('title', '=', $article['title'])->exists()) return $this->error('文章标题重复！');
        // 不存在就插入
        $articleInfo = $this->model->add($article);
        if (!$articleInfo) return $this->error('文章保存失败！');
        $articleId = $articleInfo['id'];
        // 保存文章标签
        $articleModel = Article::find($articleId); // 获取要关联的文章
        $tag = new Tag();
        $category = new Category();
        if ($data['tags']) {
            // 文章标签关联
            $articleModel->tags()->attach($data['tags']);
            // 标签使用次数
            $tag->countPlus($data['tags']);
        }
        // 保存文章分类
        if ($data['category']) {
            // 文章分类关联
            $articleModel->categorys()->attach($data['category']);
            // 分类使用次数
            $category->countPlus($data['category']);
        }
        // 返回文章信息
        return $this->success(['article' => $articleInfo, 'tag' => $data['tags'], 'category' => $data['category']]);
    }

    /**
     * 更新文章
     *
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function updateArticle(Request $request)
    {
        $data = $this->json($request->all());
        // 文章内容
        $article = [
            'id' => $data['id'],
            'title' => $data['title'],
            'abstract' => $data['abstract'],
            'cover' => $data['cover']['sortUrl'],
            'content' => $data['markdown'],
            'html' => $data['html'],
            'draft' => $data['draft'],
            'user_id' => Auth::guard('api')->id() ?? Auth::guard('client')->id()
        ];
        //更新
        $status = $this->model->where('id', '=', $data['id'])->update($article);
        if (!$status) return $this->error('文章更新失败！');

        $tagModel = new Tag();
        $catogoryModel = new Category();
        $articleModel = Article::find($data['id']);
        // 更新标签
        $tag = $this->differ($data['tagsNew'], $data['tags']);
        if ($tag['add']) {
            $articleModel->tags()->attach($tag['add']);    // 增加标签关联
            $tagModel->countPlus($tag['add']);
        }
        if ($tag['del']) {
            $articleModel->tags()->detach($tag['del']);  // 删除标签关联
            $tagModel->countPlus($tag['del'], false);
        }
        // 更新分类
        $category = $this->differ($data['categorysNew'], $data['categorys']);

        if ($category['add']) {
            $articleModel->categorys()->attach($category['add']);    // 增加分类关联
            $catogoryModel->countPlus($category['add']);
        }
        if ($category['del']) {
            $articleModel->categorys()->detach($category['del']);  // 删除标分类关联
            $catogoryModel->countPlus($category['del'], false);
        }
        // 更新时分类没变化
        if(!$category['add'] && !$category['del']){
            $articleModel->categorys()->detach($category['common']);  // 删除标分类关联
            $articleModel->categorys()->attach($category['common']);    // 增加分类关联
        }
        // 删除缓存，下次获取自动更新
        Redis::del(Rds::articleDetail($data['id']));
        Redis::del($this->redisKey($data['id']));
        if($tag['add'] || $tag['del']){
            Redis::del(Rds::articleTagDetail($data['id']));
        }
        if($category['add'] || $category['del']){
            Redis::del(Rds::articleCategoryKey($data['id']));
            Redis::del(Rds::articleCategoryDetail($data['id']));
        }
        return $this->success([$data, $tag, $category]);
    }

    /**
     * 获取文章的分类
     *
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function categorys(Request $request)
    {
        $article_id = $request->input('article_id');
        if(Redis::exists(Rds::articleCategoryKey($article_id))){
            $category = Rds::get(Rds::articleCategoryKey($article_id));
        }else{
            $articleModel = Article::find($article_id);
            if (!$articleModel) require $this->error('没找到文章！');
            $category = $articleModel->categorys->map(function ($item) {
                return $item->id;
            });
            Rds::set(Rds::articleCategoryKey($article_id), $category);
        }
        return $this->success($category);
    }

    /**
     * 获取文章分类线
     *
     * @param Request $request
     * @return void
     * @author chendaye
     */
    public function categoryLine(Request $request)
    {
        $article_id = $request->input('article_id');
        if(Redis::exists(Rds::articleCategoryDetail($article_id))){
            $category = Rds::get(Rds::articleCategoryDetail($article_id));
        }else{
            $articleModel = Article::find($article_id);
            if (!$articleModel) require $this->error('没找到文章！');
            $category = $articleModel->categorys;
            Rds::set(Rds::articleCategoryDetail($article_id), $category);
        }
        return $this->success($category);
    }

    /**
     * article绑定的标签
     *
     * @param Request $request
     * @return void
     * @author chendaye
     */
    public function tags(Request $request)
    {
        $article_id = $request->input('article_id');
        if(Redis::exists(Rds::articleTagDetail($article_id))){
            $tagDetail = Rds::get(Rds::articleTagDetail($article_id));
        }else{
            $articleModel = Article::find($article_id);
            if (!$articleModel) require $this->error('没找到文章！');
            $list = $articleModel->tags;
            $tag = $list->map(function ($item) {
                return $item->id;
            });
            $tagDetail = ['tag' => $tag, 'list' => $list];
            Rds::set(Rds::articleTagDetail($article_id), $tagDetail);
        }
        return $this->success($tagDetail);
    }

    /**
     * Undocumented function
     *
     * @param array $new
     * @param array $old
     * @return array
     * @author chendaye
     */
    protected function differ(array $new, array $old)
    {
        // 数组交集
        $common = array_intersect($new, $old);
        // 要删除的值
        $del = array_diff($old, $common);
        // 要新增的值
        $add = array_diff($new, $common);
        return [
            'common' => $common,
            'del' => $del,
            'add' => $add
        ];
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

    /**
     * 根据短地址删除图片
     *
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function imgDel(Request $request)
    {
        $lm = new Lm();
        // $sort = 'group1/M00/00/00/rBIAC10kAjeAKJZnAAAEIXjSYjYd148.png';
        $sort = $request->input('sort');
        $filename = Str::after($sort, '/');
        $group = Str::before($sort, '/');
        // 删除图片
        $res = $lm->del($group, $filename);
        if ($res === true) {
            return $this->success($res);
        } else {
            return $this->error($res);
        }
    }

    /**
     * 获取文章内容
     * @param Request $request
     * @return  array
     */
    public  function blogDetail(Request $request)
    {
        // 文章id
        $id = $request->input('id');
        if(!$id) return $this->error('参数错误！');
        if(Redis::exists(Rds::articleDetail($id))){
            $article = Rds::get(Rds::articleDetail($id));
        }else{
            // 文章内容
            $article = $this->model->find($id);
            // 文章标签
            $article->tags;
            // 文章分类
            $article->categorys;
            // 文章评论
            // 下一篇文章 前一篇文章
            $pn['next'] = $this->model->where('id', '>', $article->id)->orderBy('id', 'desc')->first();
            $pn['pre'] = $this->model->where('id', '<', $article->id)->orderBy('id', 'desc')->first();
            $article['pn'] = $pn ?? null;
            Rds::set(Rds::articleDetail($id), $article);
        }
        return $this->success($article);
    }

    /**
     * 文章归档
     * @param Request $request
     */
    public function archives(Request $request){
        $where = $request->input('time');
        $from = $where[0] ?? '2019-01-01';
        $to = $where[1] ?? '2032-12-31';

        if($from == '2019-01-01' && $to == '2032-12-31' && Redis::exists(Rds::articleArchives())){
            $archives = Rds::get(Rds::articleArchives());
        }else{
            $content = [];
            $count = 0;
            $year = ['2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030', '2031', '2032',];
            $month = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

            foreach ($year as $y) {
                foreach ($month as $m) {
                    $tmp = $y . '-' . $m;
                    $current = Carbon::parse($tmp);
                    if ($current->gte($from) && $current->lte($to)) {
                        $data = $this->model->select('id', 'title', 'abstract', 'cover', 'view', 'comment', 'user_id', 'user_name', 'created_at', 'updated_at')->where([
                            ['created_at', '>=', $from],
                            ['created_at', '<=', $to],
                            ['created_at', '>=', $tmp . '-01'],
                            ['created_at', '<=', $tmp . '-31'],
                        ])->orderBy('created_at', 'desc')->get();
                        if ($data->count() > 0) {
                            $content[$y][$m] = $data;
                            $count += $data->count();
                        }
                    }
                }
            }
            $archives = ['blog' => $content, 'total' => $count];
            Rds::set(Rds::articleArchives(), $archives);
        }
        return $this->success($archives);
    }

    /**
     * 更新文章浏览次数
     * @param Request $request
     */
    public function viewBlog(Request $request){
        $id = $request->input('id');
        $blog = $this->model->find($id);
        $blog->view++;
        $res = $blog->save();
        return $this->success($res);
    }

    /**
     * 文章数量信息
     *
     * @return void
     * @author chendaye
     */
    public function number(){
        if(Redis::exists(Rds::articleCount())){
            $count = Rds::get(Rds::articleCount());
        }else{
            $category = new Category();
            $tag = new Tag();
            $article = new Article();
            $articleCount = $article->count();
            $categoryCount = $category->count();
            $tagCount = $tag->count();
            $count = [
                'articleCount' => $articleCount,
                'categoryCount' => $categoryCount,
                'tagCount' => $tagCount,
            ];
            Rds::set(Rds::articleCount(), $count, 7200);
        }
        return $this->success($count);
    }
}
