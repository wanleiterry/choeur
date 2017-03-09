<?php

namespace App\Services;

use App\Models\Cluster;

class DotService extends BaseService
{
    public function __construct(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * 根据中心id获取网点列表
     *
     * @param $clusterId
     * @param array $params
     * @return array
     */
    public function getDotList($clusterId, array $params)
    {
        if (intval($clusterId) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        $offset = isset($params['offset']) ? $params['offset'] : OFFSET;
        $limit = isset($params['limit']) ? $params['limit'] : LIMIT;

        $dotQuery = Cluster::where('parent_id', $clusterId);

        if (isset($params['name']) && ! empty($params['name']))
            $dotQuery->where('name', 'like', '%' . $params['name'] . '%');

        if (isset($params['city']) && ! empty($params['city']))
            $dotQuery->where('city', 'like', '%' . $params['city'] . '%');

        if (isset($params['county']) && ! empty($params['county']))
            $dotQuery->where('county', 'like', '%' . $params['county'] . '%');

        if (isset($params['district']) && ! empty($params['district']))
            $dotQuery->where('district', 'like', '%' . $params['district'] . '%');

        if (isset($params['orderBy']) && ! empty($params['orderBy'])) {
            $direction = isset($params['direction']) && in_array($params['direction'], ['asc', 'desc'])
                ? $params['direction'] : 'asc';
            $dotQuery->orderBy($params['orderBy'], $direction);
        }

        $data['count'] = $dotQuery->count();

        if ($data['count'] == 0) {
            $data['data'] = [];
            return $data;
        }

        $dots = $dotQuery->select('id', 'name', 'ip', 'city', 'county', 'district', 'created_at')
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
    public function getDot($id)
    {
        $data = [];

        $cluster = Cluster::select('id', 'parent_id', 'name', 'ip', 'city', 'county', 'district')
            ->where('id', $id)
            ->where('parent_id', '>', 0)
            ->with(['cluster' => function ($q) {
                $q->select('id', 'name');
            }])
            ->first();

        if ($cluster == false) {
            $data['data'] = [];
        } else {
            $cluster = $cluster->toArray();
            $cluster['ip'] = ! empty($cluster['ip']) ? long2ip($cluster['ip']) : '';

            $data['data'] = $cluster;
        }

        return $data;
    }

    /**
     * 创建中心
     *
     * @param array $params
     * @return array
     */
    public function createDot(array $params)
    {
        if (! isset($params['parent_id']) || intval($params['parent_id']) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        if (! isset($params['ip']) || chkIpV4($params['ip']) == false)
            return ['status' => false, 'error' => '参数错误'];

        //判断中心是否存在
        if (Cluster::where(['id' => $params['parent_id']])->value('id') == false)
            return ['status' => false, 'error' => '中心不存在'];

        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $insData = [
            'parent_id' => $params['parent_id'],
            'name' => isset($params['name']) ? $params['name'] : '',
            'ip' => ip2long($params['ip']),
            'city' => isset($params['city']) ? $params['city'] : '',
            'county' => isset($params['county']) ? $params['county'] : '',
            'district' => isset($params['district']) ? $params['district'] : '',
            'created_by' => $user['id'],
        ];

        //创建
        if ($this->cluster->validate($insData) !== false) {
            if (Cluster::where(['name' => $params['name']])->value('id') != false)
                return ['status' => false, 'error' => '网点名称已存在'];

            $result = Cluster::create($insData);

            if ($result !== false)
                return ['status' => true, 'data' => $result['id']];
            else
                return ['status' => false, 'error' => '中心创建失败'];
        } else {
            return ['status' => false, 'error' => $this->cluster->getErrors()];
        }
    }

//    /**
//     * 更新中心
//     *
//     * @param $id
//     * @param array $params
//     * @return array
//     */
//    public function updateCluster($id, array $params)
//    {
//        if (intval($id) <= 0)
//            return ['status' => false, 'error' => '参数错误'];
//
//        if (isset($params['ip']) && chkIpV4($params['ip']) == false)
//            return ['status' => false, 'error' => '参数错误'];
//
//        //只能有一个中心，判断中心是否已存在
//        $cluster = Cluster::where(['id' => $id, 'parent_id' => 0])->first();
//        if ($cluster == false)
//            return ['status' => false, 'error' => '中心不存在'];
//
//        $cluster = $cluster->toArray();
//
//        //参数整理
//        $user = $this->getAuthUser();
//        if ($user == false)
//            return ['status' => false, 'error' => 'token失效'];
//        $updData = [
//            'parent_id' => 0,
//            'name' => isset($params['name']) && ! empty($params['name']) ? $params['name'] : $cluster['name'],
//            'ip' => isset($params['ip']) ? ip2long($params['ip']) : $cluster['ip'],
//            'city' => isset($params['city']) && ! empty($params['city']) ? $params['city'] : $cluster['city'],
//            'county' => isset($params['county']) && ! empty($params['county']) ? $params['county'] : $cluster['county'],
//            'district' => isset($params['district']) && ! empty($params['district']) ? $params['district'] : $cluster['district'],
//            'updated_by' => $user['id'],
//        ];
//
//        if ($this->cluster->validate($updData) !== false) {
//            $result = Cluster::where('id', $id)->update($updData);
//            if ($result !== false) {
//                $data['data'] = $id;
//                return ['status' => true, 'data' => $data];
//            } else {
//                return ['status' => false, 'error' => '中心更新失败'];
//            }
//        } else {
//            return ['status' => false, 'error' => $this->cluster->getErrors()];
//        }
//    }
//
//    /**
//     * 删除中心
//     *
//     * @param $id
//     * @return array
//     */
//    public function deleteCluster($id)
//    {
//        if (intval($id) <= 0)
//            return ['status' => false, 'error' => '参数错误'];
//
//        //只能有一个中心，判断中心是否已存在
//        $cluster = Cluster::where(['id' => $id, 'parent_id' => 0])->first();
//        if ($cluster == false)
//            return ['status' => false, 'error' => '中心不存在'];
//
//        $cluster = $cluster->toArray();
//
//        //判断中心下面是否有网点
//        if (Cluster::where(['parent_id' => $id])->value('id') != false)
//            return ['status' => false, 'error' => '中心下有网点'];
//
//        $result = Cluster::where('id', $id)->delete();
//        if ($result !== false) {
//            $data['data'] = '中心删除成功';
//            return ['status' => true, 'data' => $data];
//        } else {
//            return ['status' => false, 'error' => '中心删除失败'];
//        }
//    }
}