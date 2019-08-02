<?php

namespace App\Models;


class Category extends Model
{
    protected $table = 'categorys';

    /**
     * 反向多对多关联
     * 此分类拥有的文章
     *
     * @return array
     * @author chendaye
     */
    public function articles()
    {
        // 自动维护中间表的时间戳
        return $this->belongsToMany(Article::class, 'article_has_categorys', 'category_id',  'article_id')->whereNull('article_has_categorys.deleted_at')->withTimestamps();
    }

    /**
     * 设置分类使用次数
     *
     * @param integer|array $categoryId
     * @param boolean $plus
     * @return void
     * @author chendaye
     */
    public function countPlus($categoryId, $plus = true)
    {
        $categoryId = (array)$categoryId;
        foreach ($categoryId as $val) {
            $category = $this->find($val);
            if ($category) {
                if ($plus) {
                    $category->count++;
                } else {
                    $category->count--;
                }
                $category->save();
            }
        }
    }

    /**
     * 获取所有子评论
     *
     * @param $id
     * @param array $next
     * @return array
     */
    public function nextCategory($id, $next = [])
    {
        $tmp = $this->where('pid', $id)->orderBy('id', 'desc')->get();
        foreach ($tmp as $item){
            $next[] = $item['id'];
            //todo： 获取每一个节点的分类树
            $this->nextCategory($item['id'], $next);
        }
        return $next;
    }
}
