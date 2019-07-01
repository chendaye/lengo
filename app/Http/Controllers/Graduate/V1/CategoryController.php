<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends AuthController
{
    // 主表类名
    public $namespace = Category::class;

    /**
     * 删除分类
     *
     * @param Request $request
     * @return void
     * @author chendaye
     */
    public function delete(Request $request)
    {
        // 删除标签
        $this->del($request);
        //TODO：删除标签与
    }


    /**
     * 获取下一级节点
     *
     * @param integer $pid
     * @return array
     * @author chendaye
     */
    public function nextNode(int $pid = 1)
    {
        return $this->model->where('pid', $pid)->orderBy('created_at', 'desc')->get();
    }

    /**
     * 生成分类树
     *
     * @param integer $root
     * @param array $node
     * @return array
     * @author chendaye
     */
    public function nodeTree(int $root = 1, array $node = [])
    {
        // 获取下一级节点
        $child = $this->nextNode($root)->toArray();
        // 判断下一级节点是否存在
        foreach ($child as $val) {
            if ($this->nextNode($val['id'])->toArray()) {
                $val['label'] = $val['desc'];
                $val['children'] = array_filter($this->nodeTree($val['id'], $val), function ($v) {
                    if (is_array($v)) {
                        return $v;
                    }
                });
                $node[] = $val;
            } else {
                $val['label'] = $val['desc'];
                // $val['children'] = [];
                $node[] = $val;
            }
        }
        return $node;
    }

    /**
     * 获取整个森林
     *
     * @return array
     * @author chendaye
     */
    public function tree()
    {
        $tree = [];
        $root = $this->nextNode(0)->toArray();
        foreach ($root as $val) {
            $tree[] = $this->nodeTree($val['id']);
        }
        return $this->success($tree);
    }
}
