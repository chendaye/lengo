<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Lib\Fdfs\Lm;

class UserController extends Controller
{
    public function add(User $user, Request $request)
    {
        // 验证规则
        $rules = [
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required', 'min:6', 'max:16'],
        ];

        // 错误信息
        $messages = [
            'name.required' => '请填写正确的用户名',
            'email.required' => '请填写正确的邮箱',
            'password.required' => '请填写正确的密码',
            'password.min' => '密码必须大于等于6个字符',
        ];

        $payload = $request->only('name', 'email', 'password', 'remark', 'avatar');

        $validator = Validator::make($payload, $rules, $messages);
        // 验证格式
        if ($validator->fails()) {
            return $this->response->array(['error' => $validator->errors()]);
        }

        // 创建用户
        $result = $user->insert([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
            'remark' => $payload['remark'],
            'avatar' => $payload['avatar']
        ]);
        
        return $this->success($result);
    }

    /**
     * 头像上传
     *
     * @param Request $request
     * @return array
     * @author long
     */
    public function avatar(Request $request)
    {
        $lm = new Lm();
        $tmp = $request->file('avatar');
        $file = $lm->up((string)$tmp);
        $file['url'] = $lm->url($file['group_name'], $file['filename']);

        return $this->response->array($file);
    }
}
