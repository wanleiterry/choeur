<?php

namespace App\Services;

use App\Models\Cluster;

class ClusterService extends BaseService
{
    public function __construct(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * 获取中心信息
     *
     * @return array
     */
    public function getCluster()
    {
        $data = [];

        $cluster = Cluster::select('id', 'name', 'ip', 'city', 'county', 'district')
            ->where(['parent_id' => 0])
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
    public function createCluster(array $params)
    {
        if (! isset($params['ip']) || chkIpV4($params['ip']) == false)
            return ['status' => false, 'error' => '参数错误'];

        //只能有一个中心，判断中心是否已存在
        if (Cluster::where(['parent_id' => 0])->value('id') != false)
            return ['status' => false, 'error' => '中心已存在'];

        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $insData = [
            'parent_id' => 0,
            'name' => isset($params['name']) ? $params['name'] : '',
            'ip' => ip2long($params['ip']),
            'city' => isset($params['city']) ? $params['city'] : '',
            'county' => isset($params['county']) ? $params['county'] : '',
            'district' => isset($params['district']) ? $params['district'] : '',
            'created_by' => $user['id'],
        ];

        //创建
        if ($this->cluster->validate($insData) !== false) {
            $result = Cluster::create($insData);

            if ($result !== false)
                return ['status' => true, 'data' => $result['id']];
            else
                return ['status' => false, 'error' => '中心创建失败'];
        } else {
            return ['status' => false, 'error' => $this->cluster->getErrors()];
        }
    }

    /**
     * 更新中心
     *
     * @param $id
     * @param array $params
     * @return array
     */
    public function updateCluster($id, array $params)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        if (isset($params['ip']) && chkIpV4($params['ip']) == false)
            return ['status' => false, 'error' => '参数错误'];

        //只能有一个中心，判断中心是否已存在
        $cluster = Cluster::where(['id' => $id, 'parent_id' => 0])->first();
        if ($cluster == false)
            return ['status' => false, 'error' => '中心不存在'];

        $cluster = $cluster->toArray();

        //参数整理
        $user = $this->getAuthUser();
        if ($user == false)
            return ['status' => false, 'error' => 'token失效'];

        $updData = [
            'parent_id' => 0,
            'name' => isset($params['name']) && ! empty($params['name']) ? $params['name'] : $cluster['name'],
            'ip' => isset($params['ip']) ? ip2long($params['ip']) : $cluster['ip'],
            'city' => isset($params['city']) && ! empty($params['city']) ? $params['city'] : $cluster['city'],
            'county' => isset($params['county']) && ! empty($params['county']) ? $params['county'] : $cluster['county'],
            'district' => isset($params['district']) && ! empty($params['district']) ? $params['district'] : $cluster['district'],
            'updated_by' => $user['id'],
        ];

        //更新
        if ($this->cluster->validate($updData) !== false) {
            $result = Cluster::where('id', $id)->update($updData);
            if ($result !== false) {
                $data['data'] = $id;
                return ['status' => true, 'data' => $data];
            } else {
                return ['status' => false, 'error' => '中心更新失败'];
            }
        } else {
            return ['status' => false, 'error' => $this->cluster->getErrors()];
        }
    }

    /**
     * 删除中心
     *
     * @param $id
     * @return array
     */
    public function deleteCluster($id)
    {
        if (intval($id) <= 0)
            return ['status' => false, 'error' => '参数错误'];

        //只能有一个中心，判断中心是否已存在
        $cluster = Cluster::where(['id' => $id, 'parent_id' => 0])->first();
        if ($cluster == false)
            return ['status' => false, 'error' => '中心不存在'];

        $cluster = $cluster->toArray();

        //判断中心下面是否有网点
        if (Cluster::where(['parent_id' => $id])->value('id') != false)
            return ['status' => false, 'error' => '中心下有网点'];

        $result = Cluster::where('id', $id)->delete();

        if ($result !== false) {
            $data['data'] = '中心删除成功';
            return ['status' => true, 'data' => $data];
        } else {
            return ['status' => false, 'error' => '中心删除失败'];
        }
    }
}