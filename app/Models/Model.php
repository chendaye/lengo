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
     * 单条记录
     *
     * @param array $where
     * @return void
     * @author chendaye
     */
    public function detail($where = [], $order = ['id' => 'desc'])
    {
        $query = $this->conditions($where);
        $data = $query->first();
        return $data;
    }

    /**
     * 所有记录
     *
     * @param array $where
     * @return void
     * @author chendaye
     */
    public function lists($where, $order = [])
    {
        $query = $this->conditions($where);
        $query = $this->sort($order, $query);
        $data = $query->get();
        return $data;
    }

    /**
     *  简单条件查询
     *
     * @param array $where
     * @return $this
     * @author long
     */
    protected function conditions(array $where)
    {
        $query = $this;
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
        $insert = [];
        foreach ($data as $key => $val) {
            if (Schema::hasColumn($this->table, $key)) {
                $insert[$key] = $val;
            }
        }
        $insert['created_at'] = date( "Y-m-d H:i:s", time());
        $insert['updated_at'] = date( "Y-m-d H:i:s", time());
        // $this->save();
        $insertId =  $this->insertGetId($insert); // 返回自增ID
        $insert['id'] = $insertId;
        return $insert;

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
        $query = $this->conditions($data, $query);
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
        $query = $this;
        $query = $this->conditions($condition, $query);
        return $query->update($data);
    }
}
