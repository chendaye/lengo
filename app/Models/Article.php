<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Exists;

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
        return $this->belongsToMany(Category::class, 'article_has_categorys', 'article_id', 'category_id')->whereNull('article_has_categorys.deleted_at')->orderBy('level', 'asc')->withTimestamps();
    }

    /**
     * 分页
     */
    public function list($page = 1, $limit = 10, $where = [], $order = ['id' => 'desc'])
    {
        $query = $this;
        $query = $query->select('id', 'title', 'abstract', 'cover', 'view', 'comment', 'user_id', 'user_name','draft', 'created_at', 'updated_at');
        //todo: 根据标题搜索
        if (isset($where['title']) && !empty($where['title'])) {
            $query = $query->where([
                ['title', 'like', '%' . $where['title'] . '%']
            ]);
        }
        //todo：根据分类搜索
        if (isset($where['category']) && !empty($where['category'])) {
            $categoryHasArticle = new ArticleHasCategory();
            if(isset($where['categorySon']) && $where['categorySon'] == true){
                // 质包含父分类
                $cm = new Category();
                $cId = $cm->nextCategory($where['category'][0], []); // 所有次级分类
                $where['category'] = array_merge($where['category'],  $cId ?? []);
            }
            $articles = $categoryHasArticle->whereIn('category_id', $where['category'])
                ->whereNull('deleted_at')->get();
            if ($articles) {
                $query = $query->whereIn('id', $articles->pluck('article_id'));
            }
        }
        //todo： 根据标签搜索
        if (isset($where['tag']) && !empty($where['tag'])) {
            $tagHasArticle = new ArticleHasTag();
            $articles = $tagHasArticle->whereIn('tag_id', $where['tag'])
                ->whereNull('deleted_at')->get();

            if ($articles) {
                $query = $query->whereIn('id', $articles->pluck('article_id'));
            }
        }
        $data = $query->where('user_id', Auth::id())
        ->orderBy('articles.updated_at', 'desc')
        ->orderBy('articles.view', 'desc')
        ->paginate($limit, ['*'], 'page',  $page);
        return $data;
    }

}
