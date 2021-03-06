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
        return $this->belongsToMany(Article::class, 'article_has_tags', 'tag_id',  'article_id')->whereNull('article_has_tags.deleted_at')->withTimestamps();
    }

    /**
     * 设置标签使用次数
     *
     * @param integer|array $tagId
     * @param boolean $plus
     * @return void
     * @author chendaye
     */
    public function countPlus($tagId, $plus = true)
    {
        $tagId = (array)$tagId;
        foreach ($tagId as $val) {
            $tag = $this->find($val);
            if ($tag) {
                if ($plus) {
                    $tag->count++;
                } else {
                    $tag->count--;
                }
                $tag->save();
            }
        }
    }
}
