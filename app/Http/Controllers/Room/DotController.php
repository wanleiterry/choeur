<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DotService;

class DotController extends Controller
{
    public function __construct(DotService $dotService)
    {
        $this->dotService = $dotService;
    }

    /**
     * 获取网点列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDotList($clusterId, Request $request)
    {
        $data = $this->dotService->getDotList($clusterId, $request->all());
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 获取网点
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDot($id)
    {
        $data = $this->dotService->getDot($id);
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 创建网点
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDot(Request $request)
    {
        $data = $this->dotService->createDot($request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 更新网点
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDot($id, Request $request)
    {
        $data = $this->dotService->updateDot($id, $request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 删除网点
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDot($id)
    {
        $data = $this->dotService->deleteDot($id);

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }
}
