<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUserList(Request $request)
    {
        $users = \App\Models\User::get();
        return response()->json($users);
    }
}
