<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ArticleHasCategory;
use Illuminate\Support\Facades\Redis;
use Lib\Redis\Rds;
use Illuminate\Support\Facades\Auth;

class CategoryController extends AuthController
{
    // 主表类名
    public $namespace = Category::class;

    /**
     * 获取下一级节点
     *
     * @param integer $pid
     * @return array
     * @author chendaye
     */
    public function nextNode(int $pid)
    {
        $next = $this->model->where('pid', $pid)->orderBy('created_at', 'asc')->get();
        return $next;
    }

    /**
     * 获取所有子分类
     *
     * @param integer $pid
     * @param array $tree
     * @return array
     * @author chendaye
     */
    public function allSon(int $pid, array $tree = [], $detail = true)
    {
        $child = $this->nextNode($pid)->toArray();
        foreach ($child as $val) {
            $tree[] = $detail ? $val : $val['id'];
            $tree = $detail ? $this->allSon($val['id'], $tree) : $this->allSon($val['id'], $tree, false);
        }
        return $tree;
    }



    /**
     * 生成分类树
     *
     * @param integer $root
     * @param array $node
     * @return array
     * @author chendaye
     */
    public function nodeTree(int $root, array $node = [])
    {
        // 获取下一级节点
        $child = $this->nextNode($root)->toArray();
        // 判断下一级节点是否存在
        foreach ($child as $val) {
            if ($this->nextNode($val['id'])->toArray()) {
                // $val['label'] = $val['desc'];
                $val['children'] = array_filter($this->nodeTree($val['id'], $val), function ($v) {
                    if (is_array($v)) {
                        return $v;
                    }
                });
                $node[] = $val;
            } else {
                // $val['label'] = $val['desc'];
                $val['children'] = [];
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
        if(Redis::exists(Rds::categoryTree())){
            $tree = Rds::get(Rds::categoryTree());
        }else{
            $root = $this->nextNode(0)->toArray();
            foreach ($root as $val) {
                // 合并为一个数组
                $val['label'] = $val['desc'];
                $val['children'] = $this->nodeTree($val['id']);
                $tree = array_merge($tree, [$val]);
                // $tree[] = $this->nodeTree($val['id']);
            }
            Rds::set(Rds::categoryTree(), $tree);
        }
        return $this->success($tree);
    }

    /**
     * 删除分类
     *
     * @param Request $request
     * @return void
     * @author chendaye
     */
    public function delete(Request $request)
    {
        // 删除分类
        $where = $request->input('where');
        $where = $this->json($where);
        // parentId
        $parentId = $where['id']['va'];

        if (!$parentId) {
            return $this->error('请传递分类id');
        }
        // 获取所有次级分类
        $son = $this->allSon($parentId, [], false);
        $son[] = $parentId;

        // 软删除中间表
        foreach($son as $val){
            $category = Category::find($val);
            if (!$category) return $this->error('没有找到此分类！');
            $category->articles->map(function ($item) use ($category) {
                ArticleHasCategory::where([
                    ['category_id', '=', $category->id],
                    ['article_id', '=', $item->id],
                ])->update(['deleted_at' => date('Y-m-d H:i:s')]);
            });
        }
        // 删除
        $res = $this->model->whereIn('id', $son)->delete();
        // 删除分类缓存
        foreach($son as $v){
            Redis::del(Rds::categoryNext($v));
            Redis::del($this->redisKey($v));
        }
        Redis::del(Rds::categoryTree());
        if ($res) {
            return $this->success('分类删除成功！');
        } else {
            return $this->error('分类删除失败！');
        }
    }


    /**
     * 新增一条记录
     *
     * @param Request $request
     */
    public function add(Request $request)
    {
        $payload = $request->input();
        // 当前登录用户
        $payload['user_id'] = Auth::guard($this->guard)->id();
        // 插入数据
        $res = $this->model->add($payload);
        Redis::del(Rds::categoryTree());
        return $this->success($res);
    }
}
