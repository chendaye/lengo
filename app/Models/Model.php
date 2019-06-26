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
    public function list($page = 1, $limit = 10, $where = [], $order = ['id' => 'desc'])
    {
        // https://laravel.com/api/5.8/Illuminate/Database/Eloquent/Builder.html#method_paginate 分页api
        $query = $this;
        $query = $query->conditions($where, $query);
        $query = $query-> sort( $order, $query);
        $data = $query->paginate($limit, ['*'], 'page',  $page);
        return $data;
    }

    /**
     *  简单条件查询
     *
     * @param array $where
     * @return $this
     * @author long
     */
    private function conditions(array $where, $query)
    {
        foreach ($where as $key => $val) {
            if (Schema::hasColumn($this->table, $key)) {
                switch ($val['ex']) {
                    case 'cp':
                        // =,>.<.>=,<=,like
                        $query = $query->where($key, $val['op'], $val['va']);
                        break;
                    case 'in':
                        $query = $query->whereIn($key, $val['va']);
                        break;
                    case 'notIn':
                        $query = $query->whereNotIn($key, $val['va']);
                        break;
                    case 'null':
                        $query = $query->whereNull($key);
                        break;
                    case 'notNull':
                        $query = $query->whereNotNull($key);
                        break;
                    case 'date':
                        $query = $query->whereDate($key, $val['va']);
                        break;
                    case 'month':
                        $query = $query->whereMonth($key, $val['va']);
                        break;
                    case 'day':
                        $query = $query->whereDay($key, $val['va']);
                        break;
                    case 'year':
                        $query = $query->whereYear($key, $val['va']);
                        break;
                    case 'time':
                        $query = $query->whereTime($key, $val['va']);
                        break;
                    case 'or':
                        $query = $query->orWhere($key, $val['op'], $val['va']);
                        break;
                    case 'bt':
                        $query = $query->whereBetween($key, $val['va']);
                        break;
                    case 'notBt':
                        $query = $query->whereNotBetween($key, $val['va']);
                        break;
                    default:
                        $query = $query->where($key, $val['op'], $val['va']);
                        break;
                }
            }
        }
        return $query;
    }

    /**
     * 排序
     *
     * @param array $order
     * @return $this
     * @author long
     */
    private function sort(array $order, $query)
    {
        foreach ($order as $key => $val) {
            if (Schema::hasColumn($this->table, $key)) {
                $query = $query->orderBy($key, $val);
            }
        }
        return $query;
    }

    /**
     * 增加一条记录
     *
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        foreach ($data as $key => $val) {
            if (Schema::hasColumn($this->table, $key)) {
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
        $query = $this;
        $query = $query->conditions($data, $query);
        return $query->delete();
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
        $query = $this;
        $query = $query->conditions($data, $query);
        return $query->update($data);
    }
}
