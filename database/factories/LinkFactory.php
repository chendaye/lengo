<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Link::class, function (Faker $faker) {
    return [
        'link' => $faker->numberBetween(100,300),
        'pid' => $faker->numberBetween(0,10),
        'name' => $faker->name,
        'desc' => $faker->realText(36, 2),
        'created_at' => $faker->dateTime('now', null),
        'updated_at' => $faker->dateTime('now', null),
        'deleted_at' => null,
    ];
});
