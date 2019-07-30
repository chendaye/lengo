<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    // 鉴权方式
    protected $guard = 'api';
    // 鉴权表
    protected $guardModel = User::class;
}
