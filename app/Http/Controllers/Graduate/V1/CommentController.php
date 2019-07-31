<?php
/**
 * Created by PhpStorm.
 * User: chenxiaolong
 * Date: 2019/7/31
 * Time: 10:57
 */

namespace App\Http\Controllers\Graduate\V1;
use App\Http\Controllers\Auth\AuthController;
use App\Models\Comment;
use Illuminate\Http\Request;


class CommentController extends AuthController
{
    // ��������
    public $namespace = Comment::class;

    /**
     * ������������
     *
     * @param Request $request
     */
    public function comments(Request $request, Comment $model)
    {
        $article_id = $request->input('id');
        if($article_id){
            $comment = $model->where('article_id', $article_id)->where('pid', 0)->get();
            if($comment = $comment->toArray()){
                foreach ($comment as $key => $item){
                    $tmp = $model->where('pid', $item['id'])->get();
                    $comment[$key]['children'] = $tmp->toArray() ?? [];
                }
                return $this->success($comment);
            }

            return $this->success([]);
        }else{
            return $this->error('û������id');
        }
    }

    public function add(Request $request)
    {
        $data = $request->input('data');
        // ��Ѷ��֤��
        $tx = [];
        $tx['aid'] = ��2034464857��;
        $tx['AppSecretKey'] = ��0wt-IbkrRHHb5eEmViY9Rvg**��;
        $tx['Ticket'] = $data['ticket'];
        $tx['Randstr'] = $data['randstr'];
        $tx['UserIP'] = $request->getClientIp(); // �ͻ���ip
        return $this->success($tx);

        // ��������

        return $this->success($tx);
    }
}