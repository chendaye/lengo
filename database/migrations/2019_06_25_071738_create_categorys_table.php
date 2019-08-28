<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pid')->default(0)->comment('父级id');
            $table->char('desc', 100)->comment('分类描述');
            $table->integer('count')->default(0)->comment('使用次数');
            $table->integer('user_id')->default(0)->comment('创建人id');
            $table->tinyInteger('level')->default(1)->comment('递归层级');
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
        Schema::dropIfExists('categorys');
    }
}
