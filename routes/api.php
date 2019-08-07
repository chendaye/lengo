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

        // 文章
        $api->group(['namespace' =>  'App\Http\Controllers\Graduate\V1'], function ($api) {
            $api->group(['prefix' => 'blog'], function ($api) {
                // 文章列表
                $api->get('indexArticle', 'ArticleController@index');
                // 文章内容
                $api->get('blogDetail', 'ArticleController@blogDetail');
                // 文章评论
                $api->get('comments', 'CommentController@comments');
                // 创建评论
                $api->post('addComments', 'CommentController@add');
                // 获取所有分类
                $api->get('listCategory', 'CategoryController@list');
                // 获取所有tag
                $api->get('listTag', 'TagController@list');
                // 文章搜索
                $api->get('articleList', 'ArticleController@index');
                // 获取友链
                $api->get('linkList', 'LinkController@show');
                // 获取文章绑定的标签
                $api->get('tags', 'ArticleController@tags');
                // 获取分类树
                $api->get('tree', 'CategoryController@tree');
                // 文章归档
                $api->get('archives', 'ArticleController@archives');
                // 文章阅读次数
                $api->post('view', 'ArticleController@viewBlog');
                // 文章数量信息
                $api->get('number', 'ArticleController@number');
            });
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

                // 获取链接
                $api->get('pLink', 'LinkController@list');
                // 创建链接
                $api->post('addLink', 'LinkController@add');
                // 更新链接
                $api->post('updateLink', 'LinkController@update');
                // 删除链接
                $api->post('delLink', 'LinkController@del');
                // 链接列表
                $api->get('indexLink', 'LinkController@index');

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
                // 获取文章绑定的分类
                $api->get('categorys', 'ArticleController@categorys');
                // 获取文章绑定的标签
                $api->get('tags', 'ArticleController@tags');
            });
        });
    });
});
