<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Lib\Fdfs\Lm;

class UserController extends AuthController
{
    public function addUser(User $user, Request $request)
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
        $user->name = $payload['name'];
        $user->email = $payload['email'];
        $user->remark = $payload['remark'];
        $user->avatar = $payload['avatar'];
        $user->password = bcrypt($payload['password']);
        $result = $user->save();
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
        $file = $lm->up((string) $tmp);
        $file['url'] = $lm->url($file['group_name'], $file['filename']);
        unlink($tmp); // 删除文件
        return $this->response->array($file);
    }
}
