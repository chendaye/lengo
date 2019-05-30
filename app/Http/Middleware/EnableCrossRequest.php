<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequest
{
    /**
     * 为进来的请求设置 header.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        // 允许跨域的地址
        $allow_origin = [
            'http://localhost:8000',
            'http://localhost:9528',
            'http://www.lengo.top:9528',
        ];
        // 请求头
        $config = [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Cookie,X-CSRF-TOKEN, Accept,Authorization',
            'Access-Control-Expose-Headers' => 'Authorization,authenticated',
            'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true'
        ];
        if (in_array($origin, $allow_origin)) {
            $response->header->add($config);
        }
        return $response;
    }
}
