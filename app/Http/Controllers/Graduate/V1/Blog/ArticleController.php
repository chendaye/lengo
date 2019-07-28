<?php

namespace App\Http\Controllers\Graduate\V1\Blog;

use App\Http\Controllers\Auth\ClientAuthController;
use App\Models\Article;


class ArticleController extends ClientAuthController
{
    // 主表类名
    public $namespace = Article::class;




}
