<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Validator;
use Auth;

class UserService extends BaseService
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 获取用户列表
     *
     * @param array $params
     * @return array
     */
    public function getUserList(array $params)
    {
        $offset = isset($params['offset']) ? $params['offset'] : OFFSET;
        $limit = isset($params['limit']) ? $params['limit'] : LIMIT;

        $userQuery = User::select('id', 'username', 'mobile', 'email', 'sex', 'status');

        if (isset($params['username']) && ! empty($params['username']))
            $userQuery->where('username', 'like', '%' . $params['username'] . '%');

        if (isset($params['mobile']) && ! empty($params['mobile']))
            $userQuery->where('mobile', 'like', '%' . $params['mobile'] . '%');

        if (isset($params['email']) && ! empty($params['email']))
            $userQuery->where('email', 'like', '%' . $params['email'] . '%');

        if (isset($params['sex']) && in_array($params['sex'], [0, 1]))
            $userQuery->where('sex', $params['sex']);

        if (isset($params['status']) && in_array($params['status'], [0, 1]))
            $userQuery->where('status', $params['status']);

        if (isset($params['orderBy']) && ! empty($params['orderBy'])) {
            $direction = isset($params['direction']) && in_array($params['direction'], ['asc', 'desc'])
                ? $params['direction'] : 'asc';
            $userQuery->orderBy($params['orderBy'], $direction);
        }

        $data['count'] = $userQuery->count();

        if ($data['count'] == 0) {
            $data['data'] = [];
            return $data;
        }

        $users = $userQuery->skip($offset)
            ->take($limit)
            ->get();

        if ($users == false)
            $users = [];
        else
            $users = $users->toArray();

        $data['data'] = $users;

        return $data;
    }

    /**
     * 获取用户信息
     *
     * @param $id
     * @return array
     */
    public function getUser($id)
    {
        $data = [];

        $user = User::select('id', 'role_id', 'username', 'mobile', 'email', 'sex', 'address', 'status', 'avatar', 'created_at')
            ->where('id', $id)
            ->with(['role' => function ($q) {
                $q->select('id', 'name');
            }])
            ->first();

        if ($user == false) {
            $data['data'] = [];
        } else {
            $user = $user->toArray();
            $data['data'] = $user;
        }

        return $data;
    }

    /**
     * 创建用户
     *
     * @param array $params
     * @return array
     */
    public function createUser(array $params)
    {
        //表单验证
        $validator = Validator::make($params, [
            'role_id' => 'required|min:1|integer',
            'username' => 'required|max:60|unique:user,username',
            'password' => 'required|max:100',
            'confirm_password' => 'required|same:password',
            'avatar' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
            'sex' => 'required|integer|min:0|max:1',
            'status' => 'required|integer|min:0|max:1',
        ]);

        if ($validator->fails())
            return ['status' => false, 'error' => '参数错误'];

        if (Role::where('id', $params['role_id'])->value('id') == false)
            return ['status' => false, 'error' => '角色不存在'];

        //参数整理
        $authUser = $this->getAuthUser();
        if ($authUser == false)
            return ['status' => false, 'error' => 'token失效'];

        $insData = [
            'role_id' => $params['role_id'],
            'username' => $params['username'],
            'password' => bcrypt($params['password']),
            'avatar' => $params['avatar'],
            'mobile' => $params['mobile'],
            'email' => $params['email'],
            'sex' => $params['sex'],
            'address' => isset($params['address']) ? $params['address'] : '',
            'status' => $params['status'],
            'created_by' => $authUser['id'],
        ];

        //创建
        $result = User::create($insData);

        if ($result !== false)
            return ['status' => true, 'data' => $result['id']];
        else
            return ['status' => false, 'error' => '用户创建失败'];
    }

    /**
     * 更新用户信息，不包括密码
     *
     * @param $id
     * @param array $params
     * @return array
     */
    public function updateUser($id, array $params)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        //判断用户是否存在
        $user = User::where('id', $id)->first();
        if ($user == false)
            return ['status' => false, 'error' => '用户不存在'];

        //表单验证
        $validator = Validator::make($params, [
            'role_id' => 'required|min:1|integer',
            'username' => 'required|max:60|unique:user,username,' . $id . ',id',
            'avatar' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
            'sex' => 'required|integer|min:0|max:1',
            'status' => 'required|integer|min:0|max:1',
        ]);

        if ($validator->fails())
            return ['status' => false, 'error' => '参数错误'];

        if (Role::where('id', $params['role_id'])->value('id') == false)
            return ['status' => false, 'error' => '角色不存在'];

        //参数整理
        $authUser = $this->getAuthUser();
        if ($authUser == false)
            return ['status' => false, 'error' => 'token失效'];

        $updData = [
            'role_id' => $params['role_id'],
            'username' => $params['username'],
            'avatar' => $params['avatar'],
            'mobile' => $params['mobile'],
            'email' => $params['email'],
            'sex' => $params['sex'],
            'address' => isset($params['address']) ? $params['address'] : '',
            'status' => $params['status'],
            'updated_by' => $authUser['id'],
        ];

        //更新
        $result = User::where('id', $id)->update($updData);
        if ($result !== false) {
            $data['data'] = $id;
            return ['status' => true, 'data' => $data];
        } else {
            return ['status' => false, 'error' => '用户更新失败'];
        }
    }

    /**
     * 修改密码
     *
     * @param $id
     * @param array $params
     * @return array
     */
    public function updatePassword($id, array $params)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        //表单验证
        $validator = Validator::make($params, [
            'origin_password' => 'required',
            'password' => 'required|max:100',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails())
            return ['status' => false, 'error' => '参数错误'];

        $username = User::where('id', $id)->value('username');
        if ($username == false)
            return ['status' => false, 'error' => '用户不存在'];

        //判断用户是否存在
        if (Auth::attempt(['username' => $username, 'password' => $params['origin_password']]) == false)
            return ['status' => false, 'error' => '原始密码错误'];

        //参数整理
        $authUser = $this->getAuthUser();
        if ($authUser == false)
            return ['status' => false, 'error' => 'token失效'];

        $updData = [
            'password' => bcrypt($params['password']),
            'updated_by' => $authUser['id'],
        ];

        //更新
        $result = User::where('id', $id)->update($updData);
        if ($result !== false) {
            $data['data'] = $id;
            return ['status' => true, 'data' => $data];
        } else {
            return ['status' => false, 'error' => '修改密码失败'];
        }
    }

    /**
     * 删除用户
     *
     * @param $id
     * @return array
     */
    public function deleteUser($id)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        //判断用户是否存在
        $userId = User::where(['id' => $id])->value('id');
        if ($userId == false || $userId != $id)
            return ['status' => false, 'error' => '用户不存在'];

        $result = User::where('id', $id)->delete();
        if ($result !== false) {
            $data['data'] = '用户删除成功';
            return ['status' => true, 'data' => $data];
        } else {
            return ['status' => false, 'error' => '用户删除失败'];
        }
    }
}