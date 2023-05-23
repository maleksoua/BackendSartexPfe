<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\DeletionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentUpdateRequest;
use App\Models\Equipment;
use App\Repositories\EquipmentRepository;
use Illuminate\Http\JsonResponse;

class EquipmentController extends Controller
{

    /**
     * @param int $equipmentId
     *
     * @return JsonResponse
     */
    public function show($equipmentId)
    {
        $equipment = Equipment::with(['zone', 'zone.site', 'zone.chef'])->findOrFail($equipmentId);

        return response()->json(['status' => 'success', 'data' => $equipment], 200);
    }

    /**
     * @param $equipmentId
     * @param EquipmentUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update($equipmentId, EquipmentUpdateRequest $request)
    {
        $equipment = Equipment::findOrFail($equipmentId);

        $name = $request->input('name');

        $equipmentRepository = new EquipmentRepository($equipment);

        $equipment = $equipmentRepository->update($name);

        return response()->json(['status' => 'success', 'data' => $equipment], 200);
    }

    /**
     * @param int $equipmentId
     *
     * @return JsonResponse
     */
    public function delete($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);

        $equipmentRepository = new EquipmentRepository($equipment);

        try {
            $equipmentRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

}
