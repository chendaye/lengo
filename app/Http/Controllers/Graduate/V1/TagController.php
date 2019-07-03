<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends AuthController
{
    // 主表类名
    public $namespace = Tag::class;

    public function delete(Request $request)
    {
        // 删除标签
        $this->del($request);
        //TODO：删除关联
    }
}
