<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use App\Models\Tag;

class TagController extends AuthController
{
    // 主表类名
    public $namespace = Tag::class;
}
