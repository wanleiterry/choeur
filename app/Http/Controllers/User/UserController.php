<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Route;

class UserController extends Controller
{
    private $userService;

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
        echo 'dev-7';
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
    public function createUser(Request $request)
    {
        $data = $this->userService->createUser($request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 编辑用户
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser($id, Request $request)
    {
        $data = $this->userService->updateUser($id, $request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 修改密码
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword($id, Request $request)
    {
        $data = $this->userService->updatePassword($id, $request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 删除用户
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id)
    {
        $data = $this->userService->deleteUser($id);

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }
}
