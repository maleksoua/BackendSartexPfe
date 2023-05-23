<?php

namespace App\Http\Controllers\User;

use  App\Exceptions\EmailServiceNotAvailableException;
use App\Exceptions\PlanningExistsException;
use App\Exceptions\UnableToSaveFileException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\PlanningCreateRequest;
use App\Http\Requests\PlanningDuplicateRequest;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function profile()
    {
        $user = auth()->user();

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    /**
     * @param UserProfileUpdateRequest $request
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     */
    public function update(UserProfileUpdateRequest $request)
    {
        $firstName = $request->input('first_name', null);
        $lastName = $request->input('last_name', null);
        $email = $request->input('email', null);
        $phone = $request->input('phone', null);
        $register = $request->input('register_number', null);
        $profileImageFile = $request->file('profile_image', null);
        $filename = '';
        if (isset($profileImageFile)) {
            $filename = date('YmdHi') . $profileImageFile->getClientOriginalName();
            $profileImageFile->move(public_path('public/Image'), $filename);
            if (!$filename) {
                throw new UnableToSaveFileException();
            }
        }
        $user = auth()->user();
        $userRepository = new UserRepository($user);
        $userRepository->updateProfile($firstName, $lastName, $email, $filename, $phone, $register);

        return response()->json(['status' => 'success', 'data' => $user]);
    }

    /**
     * @param PasswordRequest $request
     *
     * @return JsonResponse
     */
    public function password(PasswordRequest $request)
    {
        $user = auth()->user();
        $password = $request->input('password');

        $userRepository = new UserRepository($user);
        try {
            $userRepository->updatePassword($password);
        } catch (EmailServiceNotAvailableException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    /**
     * @param $chefId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexPlanning($chefId, Request $request)
    {
        $superChef = auth()->id();
        $chef = User::where('super_chef_id', $superChef)->where('role', User::ROLE_CHEF)->findOrFail($chefId);

        $planningRepository = new UserRepository($chef);

        $plannings = $planningRepository->getplanning();

        return response()->json(['status' => 'success', 'data' => $plannings], 200);
    }

    /**
     * @param $chefId
     * @param PlanningCreateRequest $request
     *
     * @return JsonResponse
     * @throws \App\Exceptions\PlanningExistsException
     */
    public function createPlanning($chefId, PlanningCreateRequest $request)
    {
        $superChef = auth()->id();
        $chef = User::where('super_chef_id', $superChef)->where('role', User::ROLE_CHEF)->findOrFail($chefId);

        $startDate = $request->input('start_at');
        $startDate = isset($startDate) ? Carbon::parse($startDate, 'Africa/Tunis') : null;

        $endDate = $request->input('end_at');
        $endDate = isset($endDate) ? Carbon::parse($endDate, 'Africa/Tunis') : null;
        $guard = $request->input('guard_id');
        $zoneId = $request->input('zone_id');

        $planningRepository = new UserRepository($chef);

        try {
            $planning = $planningRepository->addPlanning($startDate, $endDate, $guard, $chefId, $zoneId);
        } catch (PlanningExistsException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }

    /**
     * @param $chefId
     * @param PlanningDuplicateRequest $request
     *
     * @return JsonResponse
     */
    public function duplicate($chefId, PlanningDuplicateRequest $request)
    {
        $superChef = auth()->id();
        $chef = User::where('super_chef_id', $superChef)->where('role', User::ROLE_CHEF)->findOrFail($chefId);

        $monthToDuplicate = $request->input('month_to_duplicate');
        $monthCount = $request->input('month_count');

        $planningRepository = new UserRepository($chef);

        $planning = $planningRepository->duplicatePlanning($monthToDuplicate, $monthCount);

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }
}
