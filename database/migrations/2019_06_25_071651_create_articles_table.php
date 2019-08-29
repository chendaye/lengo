<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('title', 100)->unique()->comment('文章标题');  // 文章标题
            $table->text('abstract')->comment('摘要');  // 摘要
            $table->string('cover', 200)->nullable()->comment('封面图片'); // 封面图片
            $table->mediumText('content')->comment('文章内容');  // 文章内容
            $table->mediumText('html')->comment('文章html内容');  // 文章内容
            $table->integer('view')->default(0)->comment('浏览次数');  // 浏览次数
            $table->integer('comment')->default(0)->comment('评论次数次数');  // 评论次数次数
            $table->integer('user_id')->default(0)->comment('创建人id');
            $table->string('user_name')->comment('创建人姓名');
            $table->tinyInteger('draft')->comment('0:文章 1:草稿');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
