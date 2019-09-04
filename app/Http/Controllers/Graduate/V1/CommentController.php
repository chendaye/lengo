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
use Illuminate\Support\Facades\Redis;
use Lib\Redis\Rds;

class CommentController extends AuthController
{
    // 主表
    public $namespace = Comment::class;

    /**
     * 获取文章评论
     *
     * @param Request $request
     */
    public function comments(Request $request, Comment $model)
    {
        $article_id = $request->input('id');
        if (Redis::exists(Rds::commentArticle($article_id))) {
            $comment = Rds::get(Rds::commentArticle($article_id));
        } else {
            $comment = $model->where('article_id', $article_id)->where('pid', 0)->orderBy('id', 'desc')->get();
            if ($comment = $comment->toArray()) {
                foreach ($comment as $key => $item) {
                    $comment[$key]['children'] = $this->nextComment($item['id'], []);
                }
            }else{
                $comment = [];
            }
            if($comment)Rds::set(Rds::commentArticle($article_id), $comment);
        }
        return $this->success($comment);
    }

    /**
     * 获取所有子评论
     *
     * @param $id
     * @param array $next
     * @return array
     */
    public function nextComment($id, $next = [])
    {
        $tmp = $this->model->where('pid', $id)->orderBy('id', 'desc')->get();
        foreach ($tmp as $item){
            $next[] = $item;
            //todo： 单根分类树 因为评论是一条线递归到底
            return $this->nextComment($item['id'], $next);
        }
        return $next;
    }


    /**
     * 新建评论
     *
     * @param Request $request
     */
    public function add(Request $request)
    {
        $data = $request->input('params');
        // todo: 腾讯验证码验证
        $tx['aid'] = config('app.tx_id');
        $tx['AppSecretKey'] = config('app.tx_key');
        $tx['Ticket'] = $data['ticket'];
        $tx['Randstr'] = $data['randstr'];
        $tx['UserIP'] = $request->getClientIp();
        $verify = $this->curl_get_https('https://ssl.captcha.qq.com/ticket/verify', $tx);
        $verify = $this->json($verify); // json字符串转为数组
        if($verify['err_msg'] === 'OK'){
            // 验证通过
            unset($data['ticket']);
            unset($data['randstr']);
            $data['created_at'] = date('Y-m-d H:i:s', time());
            $data['updated_at'] = date('Y-m-d H:i:s', time());
            $comment_id = $this->model->insertGetId($data);
            // 删掉评论缓存
            Redis::del(Rds::commentArticle($data['article_id']));
            if($comment_id) return $this->success($comment_id);
            return $this->error('评论插入失败！');

        }else{
            return $this->error('验证码验证未通过：'.json_encode([$verify, $tx]));
        }
    }

    /**
     * curl get请求
     *
     * @param $url
     * @param $data
     * @return bool|string
     */
    private function curl_get_https($url, $data){
        $url = $url.'?';
        foreach ($data as $k => $v){
            $url = $url.$k.'='.$v.'&';
        }

        $curl = curl_init(); // 启动一个CURL会话

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在

        $tmpInfo = curl_exec($curl);     //返回api的json对象

        //关闭URL请求

        curl_close($curl);

        return $tmpInfo;    //返回json对象

    }


    /**
     * curl post 请求
     *
     * @param $url
     * @param $data
     * @return bool|string
     */
    private function curl_post_https($url,$data){ // 模拟提交数据函数

        $curl = curl_init(); // 启动一个CURL会话

        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在

        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转

        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer

        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包

        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环

        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        $tmpInfo = curl_exec($curl); // 执行操作

        if (curl_errno($curl)) {

            echo 'Errno'.curl_error($curl);//捕抓异常

        }

        curl_close($curl); // 关闭CURL会话

        return $tmpInfo; // 返回数据，json格式

    }
}
