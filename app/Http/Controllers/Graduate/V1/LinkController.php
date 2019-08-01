<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use App\Models\Link;
use Illuminate\Http\Request;

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
        $p = $this->model->where('pid', 0)->orderBy('id', 'desc')->get();
        if($p){
            $p = $p->toArray();
            foreach ($p as $key => $item){
                $tmp = $this->model->where('pid', $item['id'])->orderBy('id', 'desc')->get();
                $p[$key]['list'] = $tmp ? $tmp->toArray() : [];
            }
            return $p;
        }
        return [];
    }

}
