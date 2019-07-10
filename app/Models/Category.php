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
        return $this->belongsToMany(Article::class, 'article_has_categorys', 'category_id',  'article_id')->withTimestamps();
    }
}
