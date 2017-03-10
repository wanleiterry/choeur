<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;

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

        $userQuery = User::select('id', 'username', 'nickname', 'mobile', 'email', 'qq', 'sex', 'status');

        if (isset($params['username']) && ! empty($params['username']))
            $userQuery->where('username', 'like', '%' . $params['username'] . '%');

        if (isset($params['nicknames']) && ! empty($params['nicknames']))
            $userQuery->where('nicknames', 'like', '%' . $params['nicknames'] . '%');

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

        $user = User::select('id', 'role_id', 'username', 'nickname', 'mobile', 'email', 'qq', 'sex', 'address', 'status', 'avatar', 'created_at')
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
        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $insData = [
            'role_id' => isset($params['role_id']) ? $params['role_id'] : 0,
            'username' => isset($params['username']) ? $params['username'] : '',
            'nickname' => isset($params['nickname']) ? $params['nickname'] : '',
            'password' => isset($params['password']) ? bcrypt($params['password']) : '',
            'avatar' => isset($params['avatar']) ? $params['avatar'] : '',
            'mobile' => isset($params['mobile']) ? $params['mobile'] : '',
            'email' => isset($params['email']) ? $params['email'] : '',
            'qq' => isset($params['qq']) ? $params['qq'] : '',
            'sex' => isset($params['sex']) ? $params['sex'] : '',
            'address' => isset($params['address']) ? $params['address'] : '',
            'status' => isset($params['status']) ? $params['status'] : '',
            'created_by' => $user['id'],
        ];

        //创建
        $result = User::create($insData);

        if ($result !== false)
            return ['status' => true, 'data' => $result['id']];
        else
            return ['status' => false, 'error' => '用户创建失败'];
//        if ($this->user->validate($insData) !== false) {
//            if (User::where(['name' => $params['name']])->value('id') != false)
//                return ['status' => false, 'error' => '设备名称已存在'];
//
//            $result = User::create($insData);
//
//            if ($result !== false)
//                return ['status' => true, 'data' => $result['id']];
//            else
//                return ['status' => false, 'error' => '用户创建失败'];
//        } else {
//            return ['status' => false, 'error' => $this->user->getErrors()];
//        }
    }

    /**
     * 更新设备信息
     *
     * @param $id
     * @param array $params
     * @return array
     */
    public function updateEquipment($id, array $params)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        if (isset($params['ip']) && chkIpV4($params['ip']) == false)
            return ['status' => false, 'error' => '参数错误'];

        //判断设备是否存在
        $equipment = Equipment::where(['id' => $id])->first();
        if ($equipment == false)
            return ['status' => false, 'error' => '设备不存在'];

        $equipment = $equipment->toArray();

        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $updData = [
            'cabinet_id' => isset($params['cabinet_id']) && ! empty($params['cabinet_id']) ? $params['cabinet_id'] : $equipment['cabinet_id'],
            'name' => isset($params['name']) && ! empty($params['name']) ? $params['name'] : $equipment['name'],
            'ip' => isset($params['ip']) ? ip2long($params['ip']) : $equipment['ip'],
            'port' => isset($params['port']) && ! empty($params['port']) ? $params['port'] : $equipment['port'],
            'updated_by' => $user['id'],
        ];

        //验证
        if ($this->equipment->validate($updData) !== false) {
            //判断机柜是否存在
            if (isset($updData['cabinet_id']) &&
                Cabinet::where(['id' => $updData['cabinet_id']])->value('id') == false)
                return ['status' => false, 'error' => '机柜不存在'];

            //判断是否重名
            if ($updData['name'] != $equipment['name']) {
                if (Equipment::where('name', $updData['name'])->value('id') != false)
                    return ['status' => false, 'error' => '设备名已存在'];
            }

            //更新
            $result = Equipment::where('id', $id)->update($updData);
            if ($result !== false) {
                $data['data'] = $id;
                return ['status' => true, 'data' => $data];
            } else {
                return ['status' => false, 'error' => '设备更新失败'];
            }
        } else {
            return ['status' => false, 'error' => $this->equipment->getErrors()];
        }
    }

    /**
     * 删除设备
     *
     * @param $id
     * @return array
     */
    public function deleteEquipment($id)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        //判断设备是否存在
        $equipmentId = Equipment::where(['id' => $id])->value('id');
        if ($equipmentId == false || $equipmentId != $id)
            return ['status' => false, 'error' => '设备不存在'];

        $result = Equipment::where('id', $id)->delete();
        if ($result !== false) {
            $data['data'] = '设备删除成功';
            return ['status' => true, 'data' => $data];
        } else {
            return ['status' => false, 'error' => '设备删除失败'];
        }
    }
}