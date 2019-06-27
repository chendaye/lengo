<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends BaseModel
{
    use SoftDeletes; // 软删除

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
        $query = $this->conditions($where);
        $query = $this->sort($order, $query);
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
    private function conditions(array $where)
    {
        foreach ($where as $key => $val) {
            if (Schema::hasColumn($this->table, $key)) {
                switch ($val['ex']) {
                    case 'cp':
                        // =,>.<.>=,<=,like
                        $query = $this->where($key, $val['op'], $val['va']);
                        break;
                    case 'in':
                        $query = $this->whereIn($key, $val['va']);
                        break;
                    case 'notIn':
                        $query = $this->whereNotIn($key, $val['va']);
                        break;
                    case 'null':
                        $query = $this->whereNull($key);
                        break;
                    case 'notNull':
                        $query = $this->whereNotNull($key);
                        break;
                    case 'date':
                        $query = $this->whereDate($key, $val['va']);
                        break;
                    case 'month':
                        $query = $this->whereMonth($key, $val['va']);
                        break;
                    case 'day':
                        $query = $this->whereDay($key, $val['va']);
                        break;
                    case 'year':
                        $query = $this->whereYear($key, $val['va']);
                        break;
                    case 'time':
                        $query = $this->whereTime($key, $val['va']);
                        break;
                    case 'or':
                        $query = $this->orWhere($key, $val['op'], $val['va']);
                        break;
                    case 'bt':
                        $query = $this->whereBetween($key, $val['va']);
                        break;
                    case 'notBt':
                        $query = $this->whereNotBetween($key, $val['va']);
                        break;
                    default:
                        $query = $this->where($key, $val['op'], $val['va']);
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
        $query = $this->conditions($data);
        return $query->delete();
    }

    /**
     * 更新数据
     *
     * @param array $data
     * @param array $condition
     * @return bool
     */
    public function alert($data = [], $condition = [])
    {
        $query = $this->conditions($condition);
        return $query->update($data);
    }
}
