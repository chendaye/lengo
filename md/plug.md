# laravel plugs

## composer 配置国内镜像

> 国内镜像
`composer config -g repo.packagist composer https://packagist.phpcomposer.com`

> 恢复镜像
`composer config -g --unset repos.packagist`


## laravel-permission

>管理数据库中的用户权限和角色

```php
composer require spatie/laravel-permission

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"

//如果你为你的 User 模型使用 UUID 或 GUID，你可以更新 create_permission_tables.php 的迁移，并用下面的代码替换为 $table->morphs('model') 
$table->uuid('model_id');
$table->string('model_type');

php artisan migrate

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"

https://learnku.com/articles/9842/user-role-permission-control-package-laravel-permission-usage-description
```

## dingo/api

>OAuth 服务、API 版本、Transformor 、节流等。

```php
composer require dingo/api

php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"

// Facades

// API 自带了两个 Facade，你可以酌情使用。
Dingo\Api\Facade\API
//这个是调度器的 Facade ，并提供了一些好用的辅助方法。
Dingo\Api\Facade\Route
//你可以使用这个 Facade 来获取 API 的当前路由、请求、检查当路由的名称等。

```
## tymon/jwt-auth

>JWT (JSON Web Token) 用户认证机制，支持 Laravel 和 Lumen

```php
composer require tymon/jwt-auth 1.*@rc

//  这条命令会在 config 下增加一个 jwt.php 的配置文件
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# 这条命令会在 .env 文件下生成一个加密密钥，如：JWT_SECRET=foobar
php artisan jwt:secret
```

## laravel-debugbar

>laravel-debugbar 用于直观的显示调试及错误信息，提高开发效率。

```php

composer require barryvdh/laravel-debugbar

config/app.php
'providers' => [
    ...
    Barryvdh\Debugbar\ServiceProvider::class,
],

php artisan vendor:publish   --provider="Barryvdh\Debugbar\ServiceProvider"

config/debugbar.php
'enabled' => env('APP_DEBUG', false),

https://learnku.com/laravel/t/2531
```

## laravel-ide-helper

>barryvdh/laravel-ide-helper 扩展包能让你的 IDE (PHPStorm, Sublime) 实现自动完成、代码智能提示和代码跟踪等功能，大大提高你的开发效率。

```php
composer require barryvdh/laravel-ide-helper


config/app.php
Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,

php artisan ide-helper:generate

.gitignore
.idea
_ide_helper.php
_ide_helper_models.php
.phpstorm.meta.php

https://learnku.com/laravel/t/2532
```


## Intervention/image 

>Intervention/image 是为 Laravel 定制的图片处理工具，它提供了一套易于表达的方式来创建、编辑图片

```php
composer require intervention/image

 app/config/app.php
 // 将下面代码添加到 providers 数组中
'providers' => [
    // 图片处理插件
    Intervention\Image\ImageServiceProvider::class,
    
  ],

// 将下面代码添加到 aliases 数组中
'aliases' => [
    // ...
    'Image' => Intervention\Image\Facades\Image::class,
    // ...
  ],



//此扩展包默认使用 PHP 的 GD 库来进行图像处理，但由于 GD 库对图像的处理效率要稍逊色于 imagemagick 库，因此这里推荐替换为 imagemagick 库来进行图像处理.开始之前，你得先确定本地已经安装好 GD 或 Imagick.在使用 Intervention Image 的时候，你只需要给 ImageManager 传一个数组参数就可以完成 GD 和 Imagick 库之间的互相切换.

// 引入 composer autoload
require 'vendor/autoload.php';

// 导入 Intervention Image Manager Class
use Intervention\Image\ImageManager;

// 通过指定 driver 来创建一个 image manager 实例
$manager = new ImageManager(array('driver' => 'imagick'));

// 最后创建 image 实例
$image = $manager->make('public/foo.jpg')->resize(300, 200);


//另外你也可以使用 ImageManager 的静态版本

// 引入 composer autoload
require 'vendor/autoload.php';

// 导入 Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;

// 通过指定 driver 来创建一个 image manager 实例 (默认使用 gd)
Image::configure(array('driver' => 'imagick'));

// 最后创建 image 实例
$image = Image::make('public/foo.jpg')->resize(300, 200);


php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravel5"


//运行上面的命令后，会在项目中生成 config/image.php 配置文件，打开此文件并将 driver 修改成 imagick
return array(
    'driver' => 'imagick'
);


// 基础用法
// 修改指定图片的大小
$img = Image::make('images/avatar.jpg')->resize(200, 200);

// 插入水印, 水印位置在原图片的右下角, 距离下边距 10 像素, 距离右边距 15 像素
$img->insert('images/watermark.png', 'bottom-right', 15, 10);

// 将处理后的图片重新保存到其他路径
$img->save('images/new_avatar.jpg');

/* 上面的逻辑可以通过链式表达式搞定 */
$img = Image::make('images/avatar.jpg')->resize(200, 200)->insert('images/new_avatar.jpg', 'bottom-right', 15, 10);
```

## migrations-generator

>从现存的数据中以 migration 的形式导出数据库表，包括索引和外键，相当于 数据库迁移

```php
composer require --dev "xethron/migrations-generator"

config/app.php
Way\Generators\GeneratorsServiceProvider::class,
Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class,

//If you want this lib only for dev, you can add the following code to your app/Providers/AppServiceProvider.php file, within the register() method:

public function register()
{
    if ($this->app->environment() !== 'production') {
        $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
        $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
    }
    // ...
}

https://learnku.com/laravel/t/1903
```
