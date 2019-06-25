<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');
// 路由
$api->version('v1', function ($api) {
    // 客户端路由
    $api->group(['prefix' => 'client'], function ($api) {
        // 权限验证
        $api->group(['namespace' => 'App\Http\Controllers\Auth'], function ($api) {
            $api->post('register', 'ClientAuthController@register');
            $api->post('login', 'ClientAuthController@login');
            $api->post('logout', 'ClientAuthController@logout');
            $api->post('refresh', 'ClientAuthController@refresh');
            $api->get('me', 'ClientAuthController@me');
        });
    });

    // 管理后台路由
    $api->group(['prefix' => 'admin'], function ($api) {
        // 登录登出
        $api->group(['namespace' => 'App\Http\Controllers\Auth'], function ($api) {
            $api->post('register', 'AuthController@register');
            $api->post('login', 'AuthController@login');
            $api->post('logout', 'AuthController@logout');
            $api->post('refresh', 'AuthController@refresh');
            $api->get('me', 'AuthController@me');
        });

        // Rbac
        $api->group(['namespace' =>  'App\Http\Controllers\Graduate\V1'], function ($api) {
            $api->group(['prefix' => 'rbac'], function ($api) {
                // 上传头像
                $api->post('avatar', 'UserController@avatar');
                // 添加管理员
                $api->post('add', 'UserController@add');
            });
        });

        // Wtu
        $api->group(['namespace' =>  'App\Http\Controllers\Graduate\V1'], function ($api) {
            $api->group(['prefix' => 'wtu'], function ($api) {
                // 添加标签
                $api->post('add', 'TagController@add');
            });
        });
    });
});
