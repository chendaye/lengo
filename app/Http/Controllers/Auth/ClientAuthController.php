<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientAuthController extends Controller
{
    // 鉴权方式
    protected $guard = 'client';
    // 鉴权表
    protected $guardModel = Client::class;
}
