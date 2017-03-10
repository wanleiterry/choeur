<?php

namespace App\Http\Controllers\Room;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\EquipmentService;

class EquipmentController extends Controller
{
    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * 获取机柜下的设备列表
     *
     * @param $clusterId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEquipmentList($clusterId, Request $request)
    {
        $data = $this->equipmentService->getEquipmentList($clusterId, $request->all());
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 获取设备信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEquipment($id)
    {
        $data = $this->equipmentService->getEquipment($id);
        return response()->json(['status' => true, 'data' => $data['data']]);
    }

    /**
     * 创建设备
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createEquipment(Request $request)
    {
        $data = $this->equipmentService->createEquipment($request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 更新设备信息
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEquipment($id, Request $request)
    {
        $data = $this->equipmentService->updateEquipment($id, $request->all());

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }

    /**
     * 删除设备
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteEquipment($id)
    {
        $data = $this->equipmentService->deleteEquipment($id);

        if ($data['status'] !== false) {
            return response()->json(['status' => true, 'data' => $data['data']]);
        } else {
            return response()->json(['status' => false, 'error' => $data['error']]);
        }
    }
}
