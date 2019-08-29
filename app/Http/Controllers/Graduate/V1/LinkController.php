<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use App\Models\Link;
use Illuminate\Support\Facades\Redis;
use Lib\Redis\Rds;

class LinkController extends AuthController
{
    // 主表类名
    public $namespace = Link::class;

    /**
     * 获取友链
     *
     * @return array
     */
    public function show()
    {
        if(Redis::exists(Rds::friendsLink())){
            $p = Rds::get(Rds::friendsLink());
        }else{
            $p = $this->model->where('pid', 0)->orderBy('id', 'desc')->get();
            if ($p) {
                $p = $p->toArray();
                foreach ($p as $key => $item) {
                    $tmp = $this->model->where('pid', $item['id'])->orderBy('id', 'desc')->get();
                    $p[$key]['list'] = $tmp ? $tmp->toArray() : [];
                }
                Rds::set(Rds::friendsLink(), $p);
            } else {
                $p = [];
            }
        }
        return $p;
    }

}
