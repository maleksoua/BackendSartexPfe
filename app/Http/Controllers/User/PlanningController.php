<?php

namespace App\Http\Controllers\User;

use App\Exceptions\DeletionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanningCreateRequest;
use App\Http\Requests\PlanningUpdateRequest;
use App\Models\Planning;
use App\Repositories\PlanningRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class PlanningController extends Controller
{

    /**
     * @param $planningId
     * @param PlanningUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update($planningId, PlanningUpdateRequest $request)
    {
        $superChef = auth()->id();
        $planning = Planning::whereHas('chef', function ($chef) use ($superChef) {
            $chef->where('super_chef_id', '=', $superChef);
        })->findOrFail($planningId);

        $startDate = $request->input('start_at');
        $startDate = isset($startDate) ? Carbon::parse($startDate, 'Africa/Tunis') : null;

        $endDate = $request->input('end_at');
        $endDate = isset($endDate) ? Carbon::parse($endDate, 'Africa/Tunis') : null;
        $guard = $request->input('guard_id');
        $zoneId = $request->input('zone_id');

        $planningRepository = new PlanningRepository($planning);

        $planning = $planningRepository->update($startDate, $endDate, $guard, $zoneId);

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }

    /**
     * @param int $planningId
     *
     * @return JsonResponse
     */
    public function delete($planningId)
    {
        $superChef = auth()->id();
        $planning = Planning::whereHas('chef', function ($chef) use ($superChef) {
            $chef->where('super_chef_id', '=', $superChef);
        })->findOrFail($planningId);

        $planningRepository = new PlanningRepository($planning);

        try {
            $planningRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param int $planningId
     *
     * @return JsonResponse
     */
    public function show($planningId)
    {
        $superChef = auth()->id();
        $planning = Planning::with(['planningGuard', ' zone', 'chef'])->whereHas('chef', function ($chef) use ($superChef) {
            $chef->where('super_chef_id', '=', $superChef);
        })->findOrFail($planningId);

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }

}
