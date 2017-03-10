<?php

namespace App\Http\Controllers\Room;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CabinetService;

class CabinetController extends Controller
{
    public function __construct(CabinetService $cabinetService)
    {
        $this->cabinetService = $cabinetService;
    }

    /**
     * 获取网点下的机柜列表
     *
     * @param $dotId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCabinetList($dotId, Request $request)
    {
        $data = $this->cabinetService->getCabinetList($dotId, $request->all());
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 获取机柜
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCabinet($id)
    {
        $data = $this->cabinetService->getCabinet($id);
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 创建机柜
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCabinet(Request $request)
    {
        $data = $this->cabinetService->createCabinet($request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 更新机柜
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCabinet($id, Request $request)
    {
        $data = $this->cabinetService->updateCabinet($id, $request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 删除机柜
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCabinet($id)
    {
        $data = $this->cabinetService->deleteCabinet($id);

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }
}
