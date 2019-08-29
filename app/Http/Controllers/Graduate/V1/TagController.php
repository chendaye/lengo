<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\ArticleHasTag;
use Illuminate\Support\Facades\Redis;
use Lib\Redis\Rds;

class TagController extends AuthController
{
    // 主表类名
    public $namespace = Tag::class;

    /**
     * 删除标签并且解除与文章的绑定关系
     *
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function delete(Request $request)
    {
        //TODO：软删除关联
        $where = $request->input('where');
        $tag = Tag::find($where['id']['va']);
        if(!$tag) return $this->error('没有找到此tag！');
        // $tag->articles()->detach(); // 移除所有关联的文章 硬删除
        $tag->articles->map(function ($item) use($tag) {
             ArticleHasTag::where([
                 ['tag_id', '=', $tag->id],
                 ['article_id', '=', $item->id],
             ])->update(['deleted_at' => date('Y-m-d H:i:s')]);
        });
        $this->del($request); // 删除标签本身
        return $this->success($tag);
    }
}
