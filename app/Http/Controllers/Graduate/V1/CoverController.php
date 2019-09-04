<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use App\Models\Cover;
use Illuminate\Support\Facades\Redis;
use Lib\Redis\Rds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoverController extends AuthController
{
    // 主表类名
    public $namespace = Cover::class;

    /**
     * 获取所有图片
     *
     * @return array
     */
    public function show()
    {
        if (Redis::exists(Rds::coverPic())) {
            $p = Rds::get(Rds::coverPic());
        } else {
            $p = $this->model->orderBy('id', 'desc')->get();
            Rds::set(Rds::coverPic(), $p);
        }
        return $p;
    }

    /**
     * 新增一条记录
     *
     * @param Request $request
     */
    public function add(Request $request)
    {
        $payload = $request->input();
        // 插入数据
        $res = $this->model->add($payload);
        Redis::del(Rds::coverPic());
        return $this->success($res);
    }
}
