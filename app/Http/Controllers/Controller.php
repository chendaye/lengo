<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\Response;
use Illuminate\Support\Facades\Auth;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Response;
    // 鉴权方式
    protected $guard = null;
    // 鉴权表
    protected $guardModel = null;
    // 控制器对应的的模型
    protected $model;
    // 控制器对应的模型类名称
    protected $namespace = null;

    /**
     * Controller constructor.
     * 初始化主表类名
     */
    public function __construct()
    {
        // jwt 自带中间件; token在刷新时间内，返回刷新的token
//        $this->middleware('jwt.refresh', ['except' => ['login', 'register']]);
        $this->middleware('refresh', ['except' => ['login', 'register']]); // 自定义中间件
        // 初始化主表模型
        if ($this->namespace) {
            $class = $this->namespace;
            // 初始化模型
            $this->model = new $class();
        }
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
        $model = new $this->guardModel;
        $result = $model->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
        ]);

        if ($result) {
            return $this->response->array(['success' => '恭喜！注册成功']);
        } else {
            return $this->response->array(['error' => 'Sorry！注册失败']);
        }
    }

    /**
     * 登录 guard:api 验证成功返回 jwt token
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if($request->input('name')){
            // 后台姓名登录
            $credentials = $request->only('name', 'password');
        }else{
            // 前台邮箱登录
            $credentials = $request->only('email', 'password');
        }
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
        // api
        return Auth::guard($this->guard);
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
        return $this->success($res);
    }

    /**
     * 删除记录
     *
     * @param Request $request
     * @return mixed
     */
    public function del(Request $request)
    {
        $query = $request->input('where');
        return $this->model->del($query);
    }

    /**
     * 更新记录
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $query = $request->input('where');
        $data = $request->input('data');
        return $this->model->alert($data, $query);
    }


    /**
     * 获取列表数据
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $page = $request->input('page'); // 第几页
        $limit = $request->input('limit'); // 每页条数
        $order = $request->input('order');
        $where = $request->input('where');
        // 获取的请求数据 只是把第一层转化维数组了， 第二层需要手动转换
        $order = $this->json($order);
        $where = $this->json($where);
        return $this->model->list($page, $limit, $where, $order);
    }

    /**
     * 获取某一条记录
     *
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function detail(Request $request)
    {
        $where = $request->input('where');
        // 获取的请求数据 只是把第一层转化维数组了， 第二层需要手动转换
        $where = $this->json($where);

        return $this->model->detail($where);
    }

    /**
     * 获取全部记录
     *
     * @param Request $request
     * @return array
     * @author chendaye
     */
    public function list(Request $request)
    {
        $where = $request->input('where');
        $order = $request->input('order');
        $order = $this->json($order);
        $where = $this->json($where);

        return $this->model->lists($where, $order);
    }
}
