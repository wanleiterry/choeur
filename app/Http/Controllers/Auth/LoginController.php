<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

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
     * 登录
     *
     * @param  
     * @return 
     */
    public function postLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return response()->json(['result' => '邮箱或密码不能为空。']);
        }
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['result' => '邮箱或密码错误。']);
        }
        // TODO: cookie记录登录的ip，如ip不同需重新登录
        Redis::set('user:' . $credentials['email'] . ':ip', request()->ip());
        return response()->json(['result' => $token]);
    }

    public function getLogin()
    {
        // dd(config('jwt.ttl'));
        // dd(request()->ip());
        // dd(User::get());
    }
}
