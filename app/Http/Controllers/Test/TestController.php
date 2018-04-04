<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;

class TestController extends Controller
{
    public function jwt(Request $request)
    {
        $mobile = $request->get('mobile', '18768132743');
        $password = $request->get('password', 'superadmin');
        $params = compact('mobile', 'password');
        $token = JWTAuth::attempt($params);
        echo json_encode(['token' => $token]);
        exit;
    }
}
