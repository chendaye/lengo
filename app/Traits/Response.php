<?php
namespace App\Traits;

use Dingo\Api\Routing\Helpers; // 定制化api响应
use App\Traits\StatusCode;
use Illuminate\Http\Request;

trait Response
{
    // Dingo/Api 响应
    use Helpers, StatusCode;

    /**
     * 成功响应
     *
     * @param string $msg
     * @param array $data
     * @param number $code
     * @param string $status
     * @return void
     * @author long
     */
    public function success($data = [], $msg = '', $code = null, $status = null)
    {
        $msg = $msg !== '' ? $msg : 'success';
        $code = $code !== null ? $code : 200000; // 状态码
        $status = $status !== null ? $status : true; // 状态
        // 状态信息
        $meta = $this->code($code);
        $response = [
            'meta' => $meta,
            'status' => $status,
            'message' => $msg,
            'data' => $data
        ];
        // Dingo/响应
        return $this->response->array($response);
    }

    /**
     * 自定义错误响应
     *
     * @param string $msg
     * @param number $code
     * @param string $status
     * @return void
     * @author long
     */
    public function error($msg = '', $code = null, $status = null)
    {
        $msg = $msg !== '' ? $msg : 'success';
        $code = $code !== null ? $code : 200001; // 状态码
        $status = $status !== null ? $status : false; // 状态
        // 状态信息
        $meta = $this->code($code);
        $response = [
            'meta' => $meta,
            'status' => $status,
            'message' => $msg
        ];

        // Dingo/响应
        return $this->response->array($response);
    }

    /**
     * 将请求参数转化为关联数组
     *
     * @param string $param
     * @return void
     * @author long
     */
    public function json($param)
    {
        if(\is_string($param)){
            return json_decode($param, true);
        }elseif(empty($param) || \is_null($param)){
            return [];
        }else{
            return $param;
        }
    }
}
