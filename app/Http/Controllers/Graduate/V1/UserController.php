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
        unlink($tmp); // 删除文件
        if (!$file) {
            return $this->error('FastDfs挂了，上传头像失败');
        }
        return $this->response->array($file);
    }

    /**
     * 根据id获取用户
     *
     * @param User $user
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function userInfo(User $user, Request $request)
    {
        $where = $request->input('user_id');
        // 获取的请求数据 只是把第一层转化维数组了， 第二层需要手动转换
        if ($where) {
            $res =  $user->where('id', '=', $where)->first();
            return $this->success($res);
        } else {
            return $this->error('获取用户失败！');
        }
    }
}
