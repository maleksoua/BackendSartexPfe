<?php

namespace App\Http\Controllers\User;

use App\Exceptions\DeletionException;
use App\Exceptions\PlanningExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChefPlanningCreateRequest;
use App\Http\Requests\PlanningDuplicateRequest;
use App\Models\Planning;
use App\Repositories\PlanningRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChefPlanningController extends Controller
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $chef = auth()->user();

        $planningRepository = new UserRepository($chef);

        $plannings = $planningRepository->getplanning();

        return response()->json(['status' => 'success', 'data' => $plannings], 200);
    }

    /**
     * @param ChefPlanningCreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(ChefPlanningCreateRequest $request)
    {
        $chef = auth()->user();

        $startDate = $request->input('start_at');
        $startDate = isset($startDate) ? Carbon::parse($startDate, 'Africa/Tunis') : null;

        $endDate = $request->input('end_at');
        $endDate = isset($endDate) ? Carbon::parse($endDate, 'Africa/Tunis') : null;
        $guard = $request->input('guard_id');
        $zoneId = $request->input('zone_id');

        $planningRepository = new UserRepository($chef);

        try {
            $planning = $planningRepository->addPlanning($startDate, $endDate, $guard, $chef->id, $zoneId);
        } catch (PlanningExistsException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }

    /**
     * @param $planningId
     * @param ChefPlanningCreateRequest $request
     *
     * @return JsonResponse
     */
    public function update($planningId, ChefPlanningCreateRequest $request)
    {
        $chef = auth()->id();
        $planning = Planning::where('chef_id', $chef)->findOrFail($planningId);

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
        $chef = auth()->id();
        $planning = Planning::where('chef_id', $chef)->findOrFail($planningId);

        $planningRepository = new PlanningRepository($planning);

        try {
            $planningRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param PlanningDuplicateRequest $request
     *
     * @return JsonResponse
     */
    public function duplicate(PlanningDuplicateRequest $request)
    {
        $chef = auth()->user();

        $monthToDuplicate = $request->input('month_to_duplicate');
        $monthCount = $request->input('month_count');

        $planningRepository = new UserRepository($chef);

        $planning = $planningRepository->duplicatePlanning($monthToDuplicate, $monthCount);

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }

    /**
     * @param int $planningId
     *
     * @return JsonResponse
     */
    public function show($planningId)
    {
        $chef = auth()->id();
        $planning = Planning::with(['planningGuard', 'zone', 'chef'])->where('chef_id', $chef)->findOrFail($planningId);

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }

}
