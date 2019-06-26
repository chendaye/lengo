<?php
namespace App\Traits;

/**
 * 状态码
 *
 * @author long
 */
trait StatusCode
{

    public $errorCode = [
        // 成功 失败
        200000 => '成功',
        200001 => '缺少必要的参数',
        // 登录
        500008 =>'Illegal token',
        500012=> 'Other clients logged in',
        500014 =>'Token expired',

        //文章
        503001 => '上传文件的格式不正确',
        503002 => '同步成功-记录保存失败',
        503003 => '权限错误',
        503004 => '文章保存失败',
        403017 => '临近定时时间不能取消发送任务',
        403018 => '临近定时时间不能修改发送任务',
        403019 => '超过发送时间不能发送',
        403020 => '缺少发表记录ID参数',
        //SMS
        416001 => '添加成功,审核中,请耐心等待',
        416002 => '签名添加失败',
    ];

    /**
     * 标准状态码
     *
     * @param number $code
     * @param string $message
     * @param string $data
     * @return void
     * @author long
     */
    public function code($code, $message = '')
    {
        switch ($code) {
            case 200:
                $msg = 'OK';
                $des = '如果服务器接受更新，但是在请求指定内容之外做了资源修改，必须响应200 OK以及更新的资源实例，像是向此URL发出GET请求.';
                break;
            case 201:
                $msg = 'Created';
                $des = '如果服务器需要创建一些资源, 比如创建用户, 创建用户数据, 创建资源, 默认API的create方法返回这个状态码.';
                break;
            case 301:
                $msg = 'Moved Permanently';
                $des = '被请求的资源已永久移动到新位置, 适用于资源link的变更,服务器做出兼容API.';
                break;
            case 400:
                $msg = 'Bad Request';
                $des = '请求体包含语法错误, 出现本错误服务端应该向客户端发送出错描述';
                break;
            case 401:
                $msg = 'Unauthorized';
                $des = '需要验证用户身份';
                break;
            case 403:
                $msg = 'Forbidden';
                $des = '服务器拒绝执行,服务器就算是身份验证后也不允许客户访问资源，应该响应 403 Forbidden, 适用于未登录检查.服务到期（比如付费的增值服务等）,因为某些原因不允许访问（比如被 ban ）,权限不够，403 状态码';
                break;
            case 404:
                $msg = 'Not Found';
                $des = '找不到目标资源';
                break;
            case 405:
                $msg = 'Method Not Allowed';
                $des = '不允许执行目标方法，响应中应该带有 Allow 头，内容为对该资源有效的 HTTP 方法';
                break;
            case 406:
                $msg = 'Not Acceptable';
                $des = '服务器不支持客户端请求的内容格式（比如客户端请求 JSON 格式的数据，但服务器只能提供 XML 格式的数据）';
                break;
            case 408:
                $msg = 'Request Time-out';
                $des = '请求超时！';
                break;
            case 409:
                $msg = 'Conflict';
                $des = '请求操作和资源的当前状态存在冲突';
                break;
            case 412:
                $msg = 'Precondition Failed';
                $des = '服务器在验证在请求的头字段中给出先决条件时，没能满足其中的一个或多个。主要使用场景在于实现并发控制';
                break;
            case 413:
                $msg = 'Request Entity Too Large';
                $des = 'POST 或者 PUT 请求的消息实体过大';
                break;
            case 422:
                $msg = 'Unprocessable Entity';
                $des = '请求格式正确，但是由于含有语义错误，无法响应,发送了非法的资源';
                break;
            case 428:
                $msg = 'Precondition Required';
                $des = '要求先决条件，如果想要请求能成功必须满足一些预设的条件,缺少了必要的头信息';
                break;
            case 500:
                $msg = 'Internal Server Error';
                $des = '服务器遇到了一个未曾预料的状况，导致了它无法完成对请求的处理.如：代码问题';
                break;
            case 502:
                $msg = 'Bad Gateway';
                $des = '作为网关或者代理工作的服务器尝试执行请求时，从上游服务器接收到无效的响应。';
                break;
            case 503:
                $msg = 'Service Unavailable';
                $des = '适用于: 服务器维护中.由于临时的服务器维护或者过载，服务器当前无法处理请求。
                这个状况是临时的，并且将在一段时间以后恢复。
                如果能够预计延迟时间，那么响应中可以包含一个 Retry-After 头用以标明这个延迟时间（内容可以为数字，单位为秒；或者是一个 HTTP 协议指定的时间格式）。如果没有给出这个 Retry-After 信息，那么客户端应当以处理 500 响应的方式处理它。';
                break;
            default:
                $msg = '';
                $des = isset($this->errorCode[$code]) ? $this->errorCode[$code] : '';
                break;
        }
        // 状态码以及描述
        if ($message != '') {
            $message = $msg . ':' . $message;
        } else {
            $message = $msg . ':' . $des;
        }
        // 状态信息
        $status = [
            'code' => $code,
            'message' => $message,
        ];

        return $status;
    }
}
