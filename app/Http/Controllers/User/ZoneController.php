<?php

namespace App\Http\Controllers\User;

use App\Exceptions\DeletionException;
use App\Exceptions\UnableToSaveFileException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentCreateRequest;
use App\Http\Requests\ZoneUpdateRequest;
use App\Models\Zone;
use App\Repositories\ZoneRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZoneController extends Controller
{

    /**
     * @param int $zoneId
     *
     * @return JsonResponse
     */
    public function show($zoneId)
    {
        $superChef = auth()->id();
        $zone = Zone::whereHas('site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($zoneId);

        return response()->json(['status' => 'success', 'data' => $zone], 200);
    }

    /**
     * @param $zoneId
     * @param ZoneUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update($zoneId, ZoneUpdateRequest $request)
    {
        $superChef = auth()->id();
        $zone = Zone::whereHas('site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($zoneId);
        $chef = $request->input('chef_id', null);
        $name = $request->input('name');

        $image = $request->file('image');
        $filename = '';
        if ($image) {
            $filename = date('YmdHi') . $image->getClientOriginalName();
            $image->move(public_path('public/Image'), $filename);
        }
        if (!$filename) {
            throw new UnableToSaveFileException();
        }

        $zoneRepository = new ZoneRepository($zone);

        $zone = $zoneRepository->update($name, $chef, $filename);

        return response()->json(['status' => 'success', 'data' => $zone], 200);
    }

    /**
     * @param int $zoneId
     *
     * @return JsonResponse
     */
    public function delete($zoneId)
    {
        $superChef = auth()->id();
        $zone = Zone::whereHas('site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($zoneId);

        $zoneRepository = new ZoneRepository($zone);

        try {
            $zoneRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param int $zoneId
     *
     * @return JsonResponse
     */
    public function addEquipment($zoneId, EquipmentCreateRequest $request)
    {
        $superChef = auth()->id();

        $zone = Zone::whereHas('site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($zoneId);

        $name = $request->input('name');

        $zoneRepository = new ZoneRepository($zone);

        $equipment = $zoneRepository->addEquipment($name);

        return response()->json(['status' => 'success', 'data' => $equipment], 200);
    }

    /**
     * @param $zoneId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getEquipments($zoneId, Request $request)
    {
        $superChef = auth()->id();

        $zone = Zone::whereHas('site', function ($site) use ($superChef) {
            $site->where('super_chef_id', '=', $superChef);
        })->findOrFail($zoneId);

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
