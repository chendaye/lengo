<?php

namespace App\Models;


class Tag extends Model
{
    protected $table = 'tags';

    /**
     * 反向多对多关联
     * 此标签拥有的文章
     *
     * @return array
     * @author chendaye
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_has_tags', 'tag_id',  'article_id')->withTimestamps();
    }
}
