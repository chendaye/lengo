<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
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
        throw new TokenExpiredException('jwt-auth:fuck！');
        // todo: 测试
        if(false){
            // 测试token刷新
            $token = $this->auth->parseToken()->refresh();
            return $this->setAuthenticationHeader($next($request), $token);
        }
        try{
            //todo： 检查此次请求中是否带有 token
            $this->checkForToken($request);
        }catch(UnauthorizedHttpException $e){
            throw new UnauthorizedHttpException('jwt-auth', '请求头没有提供Token！');
        }
        try {
            //todo: 检测用户的登录状态，token 是否正常，如果正常则通过
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }else{
                throw new TokenExpiredException('jwt-auth:token验证失败！');
            }
        } catch (TokenExpiredException $exception) {
            try {
                //todo: token过期
                $token = $this->auth->parseToken()->refresh();
                if(strstr($request->url(), 'admin')){
                    // 后台请求
                    Auth::guard('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                }else{
                    Auth::guard('client')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                }
                // 在响应头中返回新的 token
                return $this->setAuthenticationHeader($next($request), $token);
            } catch (JWTException $exception) {
               //todo: refresh 过期了，用户无法刷新令牌，需要重新登录，加入黑名单了 JWTException TokenBlacklistedException
                throw new UnauthorizedHttpException('jwt-auth', 'Token: 刷新时间已过：'.$exception->getMessage());
            }
        }
    }
}
