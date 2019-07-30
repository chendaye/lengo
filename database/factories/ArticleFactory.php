<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'abstract' => $faker->realText(36, 2),
        'cover' => 'group1/M00/00/00/rBIABl1AZkuAPdoDAANp9lF-pHQ572.png', // secret
        'content' => '# 一级标题
![ce66dc0e63cac68e1a7bc0aa90623b20.jpg](http://www.lengo.top:8888/group1/M00/00/00/rBIABl1AZmCAOXLmAAbQ89KxSSQ1153835)
## 二级标题
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cubGVuZ28udG9wXC9hcGlcL2NsaWVudFwvbG9naW4iLCJpYXQiOjE1NjQ0OTk2ODksImV4cCI6MTU2NDUwNjg4OSwibmJmIjoxNTY0NDk5Njg5LCJqdGkiOiJoRjlYYlhIUkcxdTVFbExRIiwic3ViIjo1LCJwcnYiOiI0MWVmYjdiYWQ3ZjZmNjMyZTI0MDViZDNhNzkzYjhhNmJkZWM2Nzc3In0.hEB03kjFkNNnm9kPCCG0-2oc1mrd5LjaIxx4jvGhLqE
# 一级标题
```js
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cubGVuZ28udG9wXC9hcGlcL2NsaWVudFwvbG9naW4iLCJpYXQiOjE1NjQ0OTk2ODksImV4cCI6MTU2NDUwNjg4OSwibmJmIjoxNTY0NDk5Njg5LCJqdGkiOiJoRjlYYlhIUkcxdTVFbExRIiwic3ViIjo1LCJwcnYiOiI0MWVmYjdiYWQ3ZjZmNjMyZTI0MDViZDNhNzkzYjhhNmJkZWM2Nzc3In0.hEB03kjFkNNnm9kPCCG0-2oc1mrd5LjaIxx4jvGhLqE
```',
        'html' => '<h1 class="my-blog-head" id="my-blog-head0">一级标题</h1><p><img src="http://www.lengo.top:8888/group1/M00/00/00/rBIABl1AZmCAOXLmAAbQ89KxSSQ1153835" alt="ce66dc0e63cac68e1a7bc0aa90623b20.jpg"></p>
<h2 class="my-blog-head" id="my-blog-head1">二级标题</h2><p>eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cubGVuZ28udG9wXC9hcGlcL2NsaWVudFwvbG9naW4iLCJpYXQiOjE1NjQ0OTk2ODksImV4cCI6MTU2NDUwNjg4OSwibmJmIjoxNTY0NDk5Njg5LCJqdGkiOiJoRjlYYlhIUkcxdTVFbExRIiwic3ViIjo1LCJwcnYiOiI0MWVmYjdiYWQ3ZjZmNjMyZTI0MDViZDNhNzkzYjhhNmJkZWM2Nzc3In0.hEB03kjFkNNnm9kPCCG0-2oc1mrd5LjaIxx4jvGhLqE</p>
<h1 class="my-blog-head" id="my-blog-head2">一级标题</h1><pre><code class="language-js">eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cubGVuZ28udG9wXC9hcGlcL2NsaWVudFwvbG9naW4iLCJpYXQiOjE1NjQ0OTk2ODksImV4cCI6MTU2NDUwNjg4OSwibmJmIjoxNTY0NDk5Njg5LCJqdGkiOiJoRjlYYlhIUkcxdTVFbExRIiwic3ViIjo1LCJwcnYiOiI0MWVmYjdiYWQ3ZjZmNjMyZTI0MDViZDNhNzkzYjhhNmJkZWM2Nzc3In0.hEB03kjFkNNnm9kPCCG0-2oc1mrd5LjaIxx4jvGhLqE</code></pre>',
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
