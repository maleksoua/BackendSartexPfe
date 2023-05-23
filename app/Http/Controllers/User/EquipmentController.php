<?php

namespace App\Http\Controllers\User;

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
        $superChef = auth()->id();
        $equipment = Equipment::with(['zone', 'zone.site', 'zone.chef'])->whereHas('zone.site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($equipmentId);

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
        $superChef = auth()->id();
        $equipment = Equipment::whereHas('zone.site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($equipmentId);
        $chef = $request->input('chef_id', null);
        $name = $request->input('name');

        $siteRepository = new EquipmentRepository($equipment);

        $equipment = $siteRepository->update($name, $chef);

        return response()->json(['status' => 'success', 'data' => $equipment], 200);
    }

    /**
     * @param int $equipmentId
     *
     * @return JsonResponse
     */
    public function delete($equipmentId)
    {
        $superChef = auth()->id();
        $equipment = Equipment::whereHas('zone.site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($equipmentId);

        $equipmentRepository = new EquipmentRepository($equipment);

        try {
            $equipmentRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

}
