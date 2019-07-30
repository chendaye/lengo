<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

// 注意，我们要继承的是 jwt 的 BaseMiddleware
class RefreshToken extends BaseMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next)
    {
        if(false){
            // 测试token刷新
            $token = $this->auth->parseToken()->refresh();
            return $this->setAuthenticationHeader($next($request), $token);
        }
        //todo： 检查此次请求中是否带有 token
        $this->checkForToken($request);

        try {
            //todo: 检测用户的登录状态，token 是否正常，如果正常则通过
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }else{
                throw new UnauthorizedHttpException('jwt-auth', 'token验证失败！');
            }
        } catch (TokenExpiredException $exception) {
            try {
                //todo: token过期
                $token = $this->auth->parseToken()->refresh();
                $api = strstr($request->url(), 'admin');
                if($api){
                    // 后台请求
                    Auth::guard('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                }else{
                    Auth::guard('client')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                }

            } catch (JWTException $exception) {
               //todo: refresh 过期了，用户无法刷新令牌，需要重新登录，加入黑名单了
                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        }

        // 在响应头中返回新的 token
        return $this->setAuthenticationHeader($next($request), $token);
    }
}
