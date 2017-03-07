<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtRefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        try {
            $newToken = JWTAuth::setRequest($request)->parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => false, 'error' => 'Token已过期']);
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'error' => 'Token无效']);
        }

        // send the refreshed token back to the client
        $response->headers->set('Authorization', 'Bearer ' . $newToken);

        return $response;
    }
}
