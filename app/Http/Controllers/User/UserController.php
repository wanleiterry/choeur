<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;

class UserController extends Controller
{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 获取用户列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserList(Request $request)
    {
        $data = $this->userService->getUserList($request->all());
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 获取用户信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        $data = $this->userService->getUser($id);
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 创建用户
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(StoreUserRequest $request)
    {echo 123;exit;
        $data = $this->userService->createUser($request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }
}
