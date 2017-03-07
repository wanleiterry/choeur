<?php

namespace App\Services;

use JWTAuth;

class BaseService
{
    public function getAuthUser()
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        return $user;
    }
}