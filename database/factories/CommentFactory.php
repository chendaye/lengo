<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Comment::class, function (Faker $faker) {
    return [
        'article_id' => $faker->numberBetween(100,300),
        'user_id' => $faker->numberBetween(1,100),
        'pid' => $faker->numberBetween(1,100),
        'content' => '{"type":"text","content":"发送到发斯蒂芬"},{"type":"emoji","content":"haha.gif"},{"type":"text","content":"是的发生的"},{"type":"emoji","content":"kelian.gif"}]',
        'raw_content' => $faker->realText(36, 2),
        'name' => $faker->name,
        'email' => $faker->email,
        'created_at' => $faker->dateTime('now', null),
        'updated_at' => $faker->dateTime('now', null),
        'deleted_at' => null,
    ];
});
