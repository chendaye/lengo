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
                $api->post('addUser', 'UserController@addUser');
                // user detail
                $api->get('user', 'UserController@userInfo');
            });
        });

        // Wtu
        $api->group(['namespace' =>  'App\Http\Controllers\Graduate\V1'], function ($api) {
            $api->group(['prefix' => 'wtu'], function ($api) {
                // 添加标签
                $api->post('addTag', 'TagController@add');
                // 标签列表数据
                $api->get('indexTag', 'TagController@index');
                // 获取标签详情
                $api->get('detailTag', 'TagController@detail');
                // 获取所有标签列表
                $api->get('listTag', 'TagController@list');
                // 更新标签
                $api->post('updateTag', 'TagController@update');
                // 删除标签
                $api->post('delTag', 'TagController@delete');

                // 获取分类树
                $api->get('tree', 'CategoryController@tree');
                // 添加分类
                $api->post('addCategory', 'CategoryController@add');
                // 编辑分类
                $api->post('updateCategory', 'CategoryController@update');
                // 删除分类
                $api->post('delCategory', 'CategoryController@delete');

                // 上传文章封面
                $api->post('cover', 'ArticleController@cover');
                // 上传插图
                $api->post('markDownPic', 'ArticleController@markDownPic');
                // 标题是否重复
                $api->get('title', 'ArticleController@checkTitle');
                // 保存文章
                $api->post('article', 'ArticleController@article');
                // 删除图片
                $api->post('imgDel', 'ArticleController@imgDel');
                // 更新文章
                $api->post('updateArticle', 'ArticleController@updateArticle');
                // 文章列表
                $api->get('indexArticle', 'ArticleController@index');
                // 文章删除
                $api->post('articleDel', 'ArticleController@del');
                // 文章发表
                $api->post('articleUpdate', 'ArticleController@update');
                // 获取文章详情
                $api->get('detailArticle', 'ArticleController@detail');
            });
        });
    });
});
