<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\Client;

class ClientAuthController extends Controller
{

    //设置使用 guard 为client选项验证
    protected $guard = 'client';

    /**
     * 权限验证
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('jwt.refresh', ['except' => ['login', 'register']]); // jwt 自带中间件
        $this->middleware('refresh', ['except' => ['login', 'register']]); // 自定义中间件
    }


    /**
     * 注册
     */
    public function register(Request $request)
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
            'password.max' => '密码最大不超过16个字符',
        ];

        $payload = $request->only('name', 'email', 'password');

        $validator = Validator::make($payload, $rules, $messages);

        // 验证格式
        if ($validator->fails()) {
            return $this->response->array(['error' => $validator->errors()]);
        }

        // 创建用户
        $result = Client::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
        ]);
        return $this->success($result);
    }

    /**
     * 登录 guard:client 验证成功返回 jwt token
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // guard:api  验证成功 返回token
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }
        // 验证失败
        return $this->response()->errorUnauthorized('登录失败');
    }

    /**
     * 当前登录用户
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->response->array($this->guard()->user());
    }

    /**
     * 登出
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();
        // 登出
        return $this->response->array(['message' => '退出成功！']);
    }

    /**
     * 刷新 返回新的 token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * 数组形式返回token.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * 获取权限验证的守卫 guard.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        // client
        return Auth::guard($this->guard);
    }
}