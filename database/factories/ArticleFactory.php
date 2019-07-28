<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'abstract' => $faker->realText(36, 2),
        'cover' => 'group1/M00/00/00/rBIACF0pTMiACd9lAAFIAjY8p3s588.png', // secret
        'content' => '**武大![颜色.png](http://www.lengo.top:8888/group1/M00/00/00/rBIACV0xZteATjtkAAACBaa2SaI1179835)**
# 一级标题
++下划线++++下划线++',
        'html' => '
<p><strong>武大<img src="http://www.lengo.top:8888/group1/M00/00/00/rBIACV0xZteATjtkAAACBaa2SaI1179835" alt="颜色.png" /></strong></p>
<h1><a id="_1"></a>一级标题</h1>
<p><ins>下划线</ins><ins>下划线</ins></p>',
        'view' => $faker->randomDigit,
        'comment' => $faker->randomDigit,
        'user_id' => 1,
        'user_name' => 'lengo',
        'draft' => 0,
        'created_at' => $faker->dateTime('now', null),
        'updated_at' => $faker->dateTime('now', null),
        'updated_at' => null,
    ];
});
