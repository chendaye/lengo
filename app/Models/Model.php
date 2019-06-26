<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Schema;

class Model extends BaseModel
{
    public $timestamps = true;
    //模型不可以注入的字段 空代表所有字段都可以注入
    protected $guarded = [];

    // 表名
    protected $table = null;

    /**
     * 分页
     */
    public function list($page = 1, $limit = 10,$where = [], $order = ['id' => 'desc'])
    {
        // https://laravel.com/api/5.8/Illuminate/Database/Eloquent/Builder.html#method_paginate 分页api
        $data = $this->conditions($where)->sort($order)->paginate($limit, ['*'], 'page',  $page);
        return $data;
    }

    /**
     * 增加一条记录
     *
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        foreach($data as $key => $val){
            if(Schema::hasColumn($this->table, $key)){
              $this->$key = $val;
            }
        }
        return $this->save();
    }

    /**
     * 删除记录
     *
     * @param array $data
     * @return bool|null
     * @throws \Exception
     */
    public function del($data = [])
    {
        return $this->conditions($data)->delete();
    }

    /**
     * 更新数据
     *
     * @param array $data
     * @param array $condiction
     * @return bool
     */
    public function alert($data = [], $condiction = [])
    {
        return $this->conditions($condiction)->update($data);
    }

    /**
     *  简单条件查询
     *
     * @param array $where
     * @return $this
     * @author long
     */
    private function conditions(array $where)
    {
        foreach ($where as $key => $val) {
            if (Schema::hasColumn($this->table, $key)) {
                switch($val['ex']){
                    case 'cp':
                        // =,>.<.>=,<=,like
                        $this->where($key, $val['op'], $val['va']);
                        break;
                    case 'in':
                        $this->whereIn($key, $val['va']);
                        break;
                    case 'notIn':
                        $this->whereNotIn($key, $val['va']);
                        break;
                    case 'null':
                        $this->whereNull($key);
                        break;
                    case 'notNull':
                        $this->whereNotNull($key);
                        break;
                    case 'date':
                        $this->whereDate($key, $val['va']);
                        break;
                    case 'month':
                        $this->whereMonth($key, $val['va']);
                        break;
                    case 'day':
                        $this->whereDay($key, $val['va']);
                        break;
                    case 'year':
                        $this->whereYear($key, $val['va']);
                        break;
                    case 'time':
                        $this->whereTime($key, $val['va']);
                        break;
                    case 'or':
                        $this->orWhere($key, $val['op'], $val['va']);
                        break;
                    case 'bt':
                        $this->whereBetween($key, $val['va']);
                        break;
                    case 'notBt':
                        $this->whereNotBetween($key, $val['va']);
                        break;
                    default :
                        $this->where($key, $val['op'], $val['va']);
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * 排序
     *
     * @param array $order
     * @return $this
     * @author long
     */
    private function sort(array $order)
    {
        foreach ($order as $key => $val) {
            if (Schema::hasColumn($this->table, $key)) {
                $this->orderBy($key, $val);
            }
        }
        return $this;
    }

}
