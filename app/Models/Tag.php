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

    /**
     * 设置标签使用次数
     *
     * @param integer $tagId
     * @param boolean $plus
     * @return void
     * @author chendaye
     */
    public function countPlus(int $tagId, $plus = true)
    { 
        $tag = $this->find($tagId);
        if($tag){
            if($plus){
                $tag->count++;
            }else{
                $tag->count--;
            }
            $tag->save();
        }
    }
}
