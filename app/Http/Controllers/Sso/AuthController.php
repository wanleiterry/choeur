<?php


namespace App\Http\Controllers\Sso;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    const SSO_REDIS_KEY = 'laravel_sso_';

    public function api()
    {
        return response()->json(['boolean' => 1]);
    }

    public function index(Request $request)
    {
        $cookieToken = $request->cookies->get('laravel_sso');
        if ($cookieToken) {
            return redirect()->intended($this->redirectPath($request) . '?token=' . $cookieToken);
        }

        $redirectUrl = $request->get('redirectUrl');
        return view('/sso/login')->with('redirectUrl', $redirectUrl);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required', 'password' => 'required',
        ]);
        $credentials = $request->only('mobile', 'password');
        if (Auth::guard()->attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();
            return $this->sendLoginResponse($request, $user);
        } else {
            return $this->sendFailedLoginResponse($request);
        }
    }

    protected function sendLoginResponse(Request $request, User $user)
    {
        $token = 'abcdefg';
        $session = $request->session();
        $session->regenerate();

//        $session->put('laravel_sso', $token);
//        $response->headers->setCookie(new Cookie(
//            $session->getName(), $session->getId(), 0, 'dev.choeur.com'
//        ));
        Cookie::queue('laravel_sso', $token);

        //将token放入redis中
        Redis::set(self::SSO_REDIS_KEY, $user['id']);

        return redirect()->intended($this->redirectPath($request) . '?token=' . $token);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only('mobile', 'remember'))
            ->withErrors([
                'mobile' => Lang::get('auth.failed'),
            ]);
    }

    protected function redirectPath(Request $request)
    {
        return $request->get('redirectUrl', '/');
    }

    public function token(Request $request)
    {
        $token = $request->get('token', '');
        //根据token认证，获取uid
        if ($token && ($token == Redis::get(self::SSO_REDIS_KEY))) {
            return response()->json(['errCode' => 0, 'uid' => 1]);
        } else {
            return response()->json([
                'errCode' => 1,
                'token' => $token,
                'cookie' => $_COOKIE
            ]);
        }
    }
}