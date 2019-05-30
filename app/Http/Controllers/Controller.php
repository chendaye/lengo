<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers; // 定制化api响应


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

    // public function __construct()
    // {
    //     parent::__construct();
    //     self::cross;
    // }

    // private function cross()
    // {
    //     // 跨域请求
    //     header( 'Access-Control-Allow-Origin:*');
    //     header( 'Access-Control-Allow-Methods:GET,POST,PATCH,PUT.OPTIONS');
    // }
}
