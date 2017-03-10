<?php

namespace App\Services;

use App\Models\Cabinet;
use App\Models\Cluster;
use App\Models\Equipment;

class CabinetService extends BaseService
{
    public function __construct(Cabinet $cabinet)
    {
        $this->cabinet = $cabinet;
    }

    /**
     * 根据网点id获取机柜列表
     *
     * @param $dotId
     * @param array $params
     * @return array
     */
    public function getCabinetList($dotId, array $params)
    {
        if (intval($dotId) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        $offset = isset($params['offset']) ? $params['offset'] : OFFSET;
        $limit = isset($params['limit']) ? $params['limit'] : LIMIT;

        $cabinetQuery = Cabinet::where('dot_id', $dotId);

        if (isset($params['name']) && ! empty($params['name']))
            $cabinetQuery->where('name', 'like', '%' . $params['name'] . '%');

        if (isset($params['orderBy']) && ! empty($params['orderBy'])) {
            $direction = isset($params['direction']) && in_array($params['direction'], ['asc', 'desc'])
                ? $params['direction'] : 'asc';
            $cabinetQuery->orderBy($params['orderBy'], $direction);
        }

        $data['count'] = $cabinetQuery->count();

        if ($data['count'] == 0) {
            $data['data'] = [];
            return $data;
        }

        $dots = $cabinetQuery->select('id', 'name', 'ip', 'created_at')
            ->skip($offset)
            ->take($limit)
            ->get();

        if ($dots == false)
            $dots = [];
        else
            $dots = $dots->toArray();

        $data['data'] = $dots;

        return $data;
    }

    /**
     * 获取网点信息
     *
     * @param $id
     * @return array
     */
    public function getCabinet($id)
    {
        $data = [];

        $cabinet = Cabinet::select('id', 'dot_id', 'name', 'ip')
            ->where('id', $id)
            ->with(['dot' => function ($q) {
                $q->select('id', 'name')->where('parent_id', '>', 0);
            }])
            ->first();

        if ($cabinet == false) {
            $data['data'] = [];
        } else {
            $cabinet = $cabinet->toArray();
            $cabinet['ip'] = ! empty($cabinet['ip']) ? long2ip($cabinet['ip']) : '';

            $data['data'] = $cabinet;
        }

        return $data;
    }

    /**
     * 创建机柜
     *
     * @param array $params
     * @return array
     */
    public function createCabinet(array $params)
    {
        if (! isset($params['dot_id']) || intval($params['dot_id']) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        if (! isset($params['ip']) || chkIpV4($params['ip']) == false)
            return ['status' => false, 'error' => '参数错误'];

        //判断网点是否存在
        if (Cluster::where('id', $params['dot_id'])->where('parent_id', '>', 0)->value('id') == false)
            return ['status' => false, 'error' => '网点不存在'];

        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $insData = [
            'dot_id' => $params['dot_id'],
            'name' => isset($params['name']) ? $params['name'] : '',
            'ip' => ip2long($params['ip']),
            'created_by' => $user['id'],
        ];

        //创建
        if ($this->cabinet->validate($insData) !== false) {
            if (Cabinet::where(['name' => $params['name']])->value('id') != false)
                return ['status' => false, 'error' => '网点名称已存在'];

            $result = Cabinet::create($insData);

            if ($result !== false)
                return ['status' => true, 'data' => $result['id']];
            else
                return ['status' => false, 'error' => '机柜创建失败'];
        } else {
            return ['status' => false, 'error' => $this->cabinet->getErrors()];
        }
    }

    /**
     * 更新机柜信息
     *
     * @param $id
     * @param array $params
     * @return array
     */
    public function updateCabinet($id, array $params)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        if (isset($params['ip']) && chkIpV4($params['ip']) == false)
            return ['status' => false, 'error' => '参数错误'];

        //判断网点是否存在
        if (isset($params['dot_id']) &&
            Cluster::where('id', $params['dot_id'])->where('parent_id', '>', 0) != false)
            return ['status' => false, 'error' => '网点不存在'];

        //判断机柜是否存在
        $cabinet = Cabinet::where(['id' => $id])->first();
        if ($cabinet == false)
            return ['status' => false, 'error' => '机柜不存在'];

        $cabinet = $cabinet->toArray();

        //判断是否重名
        if (isset($params['name']) && $params['name'] != $cabinet['name']) {
            if (Cabinet::where('name', $params['name'])->value('id') != false)
                return ['status' => false, 'error' => '机柜名已存在'];
        }

        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $updData = [
            'dot_id' => isset($params['dot_id']) && ! empty($params['dot_id']) ? $params['dot_id'] : $cabinet['dot_id'],
            'name' => isset($params['name']) && ! empty($params['name']) ? $params['name'] : $cabinet['name'],
            'ip' => isset($params['ip']) ? ip2long($params['ip']) : $cabinet['ip'],
            'updated_by' => $user['id'],
        ];

        if ($this->cabinet->validate($updData) !== false) {
            $result = Cabinet::where('id', $id)->update($updData);
            if ($result !== false) {
                $data['data'] = $id;
                return ['status' => true, 'data' => $data];
            } else {
                return ['status' => false, 'error' => '网点更新失败'];
            }
        } else {
            return ['status' => false, 'error' => $this->cabinet->getErrors()];
        }
    }

    /**
     * 删除机柜
     *
     * @param $id
     * @return array
     */
    public function deleteCabinet($id)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        //判断机柜是否存在
        $cabinetId = Cabinet::where(['id' => $id])->value('id');
        if ($cabinetId == false || $cabinetId != $id)
            return ['status' => false, 'error' => '机柜不存在'];

        //判断机柜下面是否有设备
        if (Equipment::where(['cabinet_id' => $id])->value('id') != false)
            return ['status' => false, 'error' => '机柜下有设备'];

        $result = Cabinet::where('id', $id)->delete();
        if ($result !== false) {
            $data['data'] = '机柜删除成功';
            return ['status' => true, 'data' => $data];
        } else {
            return ['status' => false, 'error' => '机柜删除失败'];
        }
    }
}