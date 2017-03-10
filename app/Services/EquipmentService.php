<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Cabinet;

class EquipmentService extends BaseService
{
    public function __construct(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    /**
     * 根据机柜id获取设备列表
     *
     * @param $cabinetId
     * @param array $params
     * @return array
     */
    public function getEquipmentList($cabinetId, array $params)
    {
        if (intval($cabinetId) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        $offset = isset($params['offset']) ? $params['offset'] : OFFSET;
        $limit = isset($params['limit']) ? $params['limit'] : LIMIT;

        $equipmentQuery = Equipment::where('cabinet_id', $cabinetId);

        if (isset($params['name']) && ! empty($params['name']))
            $equipmentQuery->where('name', 'like', '%' . $params['name'] . '%');

        if (isset($params['orderBy']) && ! empty($params['orderBy'])) {
            $direction = isset($params['direction']) && in_array($params['direction'], ['asc', 'desc'])
                ? $params['direction'] : 'asc';
            $equipmentQuery->orderBy($params['orderBy'], $direction);
        }

        $data['count'] = $equipmentQuery->count();

        if ($data['count'] == 0) {
            $data['data'] = [];
            return $data;
        }

        $equipments = $equipmentQuery->select('id', 'name', 'ip', 'port', 'created_at')
            ->skip($offset)
            ->take($limit)
            ->get();

        if ($equipments == false)
            $equipments = [];
        else
            $equipments = $equipments->toArray();

        foreach ($equipments as &$equipment) {
            $equipment['ip'] = ! empty($equipment['ip']) ? long2ip($equipment['ip']) : '';
        }

        $data['data'] = $equipments;

        return $data;
    }

    /**
     * 获取设备信息
     *
     * @param $id
     * @return array
     */
    public function getEquipment($id)
    {
        $data = [];

        $equipment = Equipment::select('id', 'cabinet_id', 'name', 'ip', 'port')
            ->where('id', $id)
            ->with(['cabinet' => function ($q) {
                $q->select('id', 'name');
            }])
            ->first();

        if ($equipment == false) {
            $data['data'] = [];
        } else {
            $equipment = $equipment->toArray();
            $equipment['ip'] = ! empty($equipment['ip']) ? long2ip($equipment['ip']) : '';

            $data['data'] = $equipment;
        }

        return $data;
    }

    /**
     * 创建设备
     *
     * @param array $params
     * @return array
     */
    public function createEquipment(array $params)
    {
        if (! isset($params['cabinet_id']) || intval($params['cabinet_id']) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        if (! isset($params['ip']) || chkIpV4($params['ip']) == false)
            return ['status' => false, 'error' => '参数错误'];

        //判断机柜是否存在
        if (Cabinet::where(['id' => $params['cabinet_id']])->value('id') == false)
            return ['status' => false, 'error' => '机柜不存在'];

        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $insData = [
            'cabinet_id' => $params['cabinet_id'],
            'name' => isset($params['name']) ? $params['name'] : '',
            'ip' => ip2long($params['ip']),
            'port' => isset($params['port']) ? $params['port'] : '',
            'created_by' => $user['id'],
        ];

        //创建
        if ($this->equipment->validate($insData) !== false) {
            if (Equipment::where(['name' => $params['name']])->value('id') != false)
                return ['status' => false, 'error' => '设备名称已存在'];

            $result = Equipment::create($insData);

            if ($result !== false)
                return ['status' => true, 'data' => $result['id']];
            else
                return ['status' => false, 'error' => '中心创建失败'];
        } else {
            return ['status' => false, 'error' => $this->equipment->getErrors()];
        }
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