<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ClusterService;

class ClusterController extends Controller
{
    public function __construct(ClusterService $clusterService)
    {
        $this->clusterService = $clusterService;
    }

    //获取中心，只有一个中心
    public function getCluster()
    {
        $data = $this->clusterService->getCluster();
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    //创建中心
    public function createCluster(Request $request)
    {
        $data = $this->clusterService->createCluster($request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    public function updateCluster($id, Request $request)
    {
        $data = $this->clusterService->updateCluster($id, $request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    public function deleteCluster($id)
    {
        $data = $this->clusterService->deleteCluster($id);

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }
    
    //创建网点
    public function createNetPoint(Request $request)
    {
        # code...
    }
}
