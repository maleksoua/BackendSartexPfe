<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Repositories\ZoneRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChefZoneController extends Controller
{

    /**
     * @param int $zoneId
     *
     * @return JsonResponse
     */
    public function show($zoneId)
    {
        $chef = auth()->id();

        $zone = Zone::where('chef_id', $chef)->findOrFail($zoneId);

        return response()->json(['status' => 'success', 'data' => $zone], 200);
    }

    /**
     * @param $zoneId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getEquipments($zoneId, Request $request)
    {
        $chef = auth()->id();

        $zone = Zone::where('chef_id', $chef)->findOrFail($zoneId);

        $page = $request->input('page', null);
        $search = $request->input('search', null);
        $perPage = $request->input('perPage', null);
        $orderBy = $request->input('orderBy', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');

        $zoneRepository = new ZoneRepository($zone);
        $equipments = $zoneRepository->paginateEquipments($page, $perPage, $search, $orderBy, $orderDirection);

        return response()->json(['status' => 'success', 'data' => $equipments], 200);
    }

}
