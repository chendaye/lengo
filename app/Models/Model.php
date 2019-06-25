<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public $timestamps = true;
    //模型不可以注入的字段 空代表所有字段都可以注入
    protected $guarded = [];
    //模型可以注入的字段
    //    protected $fillable = [];
}
