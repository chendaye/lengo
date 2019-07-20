<?php

namespace App\Models;


class Article extends Model
{
    protected $table = 'articles';

    /**
     * 文章拥有的tag
     *
     * @return array
     * @author chendaye
     */
    public function tags()
    {
        // 自动维护中间表的时间戳
        return $this->belongsToMany(Tag::class, 'article_has_tags', 'article_id', 'tag_id')->whereNull('article_has_tags.deleted_at')->withTimestamps();
    }

    /**
     * 文章拥有的分类 多对多关联
     *
     * @return void
     * @author chendaye
     */
    public function categorys()
    {
        // 自动维护中间表的时间戳
        return $this->belongsToMany(Category::class, 'article_has_categorys', 'article_id', 'category_id')->withTimestamps();
    }
}
