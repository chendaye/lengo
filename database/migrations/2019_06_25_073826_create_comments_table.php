<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('article_id')->default(0)->comment('文章id');
            $table->integer('user_id')->default(0)->comment('创建人id');
            $table->integer('pid')->default(0)->comment('父级评论id');
            $table->char('name', 60)->unique()->comment('评论人姓名');
            $table->char('email', 60)->unique()->comment('评论人邮箱');
            $table->text('content')->comment('评论内容');
            $table->text('raw_content')->comment('评论raw内容');
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
        Schema::dropIfExists('comments');
    }
}
