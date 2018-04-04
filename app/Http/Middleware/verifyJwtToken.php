<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class verifyJwtToken
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
        try {
            $token = JWTAuth::getToken();
            if ($token == false) {
                return response()->json(['boolean' => 0, 'data' => [], 'msg' => 'Token解析失败']);
            }
            $payload = JWTAuth::getPayload($token)->toArray();
            /**
             * {
             * "sub":1,
             * "iss":"http:\/\/dev.choeur.com\/api\/test\/jwt",
             * "iat":1512114280,
             * "exp":1512117880,
             * "nbf":1512114280,
             * "jti":"9950283ae3f97ca6d094fa290752b42f"}
             */
//            echo json_encode($payload);exit;

//            if (!isset($payload))
        } catch (JWTException $e) {
            //$e->getMessage();
            return response()->json(['boolean' => 0, 'data' => [], 'msg' => 'Token解析失败']);
        }
        return $next($request);
    }
}
