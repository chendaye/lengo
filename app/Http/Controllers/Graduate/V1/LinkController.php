<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends AuthController
{
    // 主表类名
    public $namespace = Link::class;

}
