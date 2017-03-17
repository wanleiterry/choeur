<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * 用户登录：账号（邮箱/手机）/密码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLogin(Request $request)
    {
        $params = $request->only('account', 'password');
        if (empty($params['account']) || empty($params['password'])) {
            return response()->json(['status' => false, 'error' => '账号或密码不能为空。']);
        }

        $credentials = [];
        //判断邮箱或手机号登录
        if (preg_match("/^1[34578]\d{9}$/", $params['account'])) {
            $credentials['mobile'] = $params['account'];
        } else {
            $credentials['email'] = $params['account'];
        }
        $credentials['password'] = $params['password'];
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['status' => false, 'error' => '邮箱或密码错误。']);
        }
        //redis记录登录的ip，如ip不同需重新登录
        $user = JWTAuth::toUser($token);
        Redis::set('user:' . $user['id'] . ':ip', request()->ip());

        // send the refreshed token back to the client
        // $response->headers->set('Authorization', 'Bearer ' . $newToken);
        $jwtHeader = ['Authorization' => 'Bearer ' . $token];

        return response()->json(['status' => true, 'data' => $token], 200, $jwtHeader);
    }

}
