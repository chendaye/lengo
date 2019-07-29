<?php

namespace App\Http\Controllers\Graduate\V1\Blog;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Lib\Fdfs\Lm;
use App\Models\Article;


class ArticleController extends AuthController
{
    // 主表类名
    public $namespace = Article::class;
}
