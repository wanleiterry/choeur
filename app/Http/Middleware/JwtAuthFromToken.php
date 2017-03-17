<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use Exception;

class JwtAuthFromToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = '';
        try {
            $token = JWTAuth::setRequest($request)->getToken() ?: $request->get('token');
            if (empty($token)) {
                return response()->json(['status' => false, 'error' => 'Token不能为空']);
            }
            // 如果用户登陆后的请求ip变化，需重新登录
            $user = JWTAuth::toUser($token);
            if (Redis::get('user:' . $user['email'] . ':ip') != $request->ip() &&
                Redis::get('user:' . $user['mobile'] . ':ip') != $request->ip()) {
                JWTAuth::invalidate($token);
                return response()->json(['status' => false, 'error' => '请重新登录']);
            }

//            //中心管理员
//            if ($user['role_id'] == 2) {
//                $params = $request->all();
//                $url = $params['_url'];
//                unset($params['_url']);
//                echo json_encode(
//                    [
//                        'method' => $request->getMethod(),
//                        'url' => $url,
//                        'content' => $params,
//                        'path' => $request->path(),
//                    ]
//                );
//            }
//            exit;
        } catch (Exception $e) {
            // 如果用户登陆后的所有请求没有jwt的token抛出异常
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => false, 'error' => 'Token无效']);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                //判断是否可以用现有过期token获取新token
                if (! empty($token)) {
                    try {
                        $newToken = JWTAuth::refresh($token);
                        JWTAuth::setToken($newToken);
                    } catch (Exception $e) {
                        if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                            return response()->json(['status' => false, 'error' => 'Token已过期']);
                        } else {
                            return response()->json(['status' => false, 'error' => '出错了']);
                        }
                    }
                }
            } else {
                return response()->json(['status' => false, 'error' => '出错了']);
            }
        }

        return $next($request);
    }
}
