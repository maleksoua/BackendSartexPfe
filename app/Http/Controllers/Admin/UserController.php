<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\DeletionException;
use App\Exceptions\EmailServiceNotAvailableException;
use App\Exceptions\PlanningExistsException;
use App\Exceptions\UnableToSaveFileException;
use App\Http\Requests\PlanningCreateRequest;
use App\Http\Requests\PlanningDuplicateRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Repositories\MailRepository;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->input('page', null);
        $search = $request->input('search', null);
        $perPage = $request->input('perPage', null);
        $orderBy = $request->input('orderBy', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');
        $role = $request->input('role');
        $role = $role ? intval($role) : $role;

        $users = UserRepository::paginate($page, $perPage, $search, $orderBy, $orderDirection, $role);

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    /**
     * @param int $userId
     *
     * @return JsonResponse
     */
    public function show($userId)
    {
        $user = User::with(['superChef', 'site', 'zones', 'superChef', 'chefs'])->findOrFail($userId);

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    /**
     * @param UserCreateRequest $request
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     * @throws EmailServiceNotAvailableException
     */
    public function create(UserCreateRequest $request)
    {
        $role = intval($request->input('role'));
        $phone = $request->input('phone');
        $email = $request->input('email');
        $password = $request->input('password');
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $register = $request->input('register_number');
        $sendEmail = (bool)$request->input('send_email', null);
        $superChef = $request->input('super_chef', null);
        $site = $request->input('site', null);
        $zones = $request->input('zones', []);
        $chefs = $request->input('chefs', []);

        $profileFile = $request->file('profile_image');
        $filename = '';
        if ($profileFile) {
            $filename = date('YmdHi') . $profileFile->getClientOriginalName();
            $profileFile->move(public_path('public/Image'), $filename);
        }
        if (!$filename) {
            throw new UnableToSaveFileException();
        }

        $user = UserRepository::create($filename, $firstName, $lastName, $email, $role, $password, $register, $phone, $superChef, $site, $zones, $chefs);

        if ($sendEmail) {
            MailRepository::sendPasswordCreateMail($user, $password);
        }

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    /**
     * @param int $userId
     * @param UserUpdateRequest $request
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     */
    public function update($userId, UserUpdateRequest $request)
    {
        $user = User::where('id', '!=', auth()->id())->findOrFail($userId);
        $userRepository = new UserRepository($user);

        $email = $request->input('email');
        $phone = $request->input('phone', null);
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $register = $request->input('register_number');
        $password = $request->input('password');
        $superChef = $request->input('super_chef', null);
        $site = $request->input('site', null);
        $zoneIds = $request->input('zones', []);
        $chefs = $request->input('chefs', []);

        $profileImageFile = $request->file('profile_image', null);
        $filename = '';
        if (isset($profileImageFile)) {
            $filename = date('YmdHi') . $profileImageFile->getClientOriginalName();
            $profileImageFile->move(public_path('public/Image'), $filename);
            if (!$filename) {
                throw new UnableToSaveFileException();
            }
        }

        $userRepository->update($firstName, $lastName, $email, $filename, $phone, $password, $register, $superChef, $site, $zoneIds, $chefs);

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    /**
     * @param int $userId
     *
     * @return JsonResponse
     */
    public function delete($userId)
    {
        $user = User::where('id', '!=', auth()->id())->findOrFail($userId);

        $usersCount = User::where('role', User::ROLE_ADMIN)->count();

        if ($usersCount <= 1 && $user->role == User::ROLE_ADMIN) {
            return response()->json(['status' => 'error', 'message' => 'admin.delete_not_allowed'], 403);
        }

        $userRepository = new UserRepository($user);

        try {
            $userRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param $chefId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexPlanning($chefId, Request $request)
    {
        $chef = User::where('role', User::ROLE_CHEF)->findOrFail($chefId);

        $planningRepository = new UserRepository($chef);

        $plannings = $planningRepository->getplanning();

        return response()->json(['status' => 'success', 'data' => $plannings], 200);
    }

    /**
     * @param $chefId
     * @param PlanningCreateRequest $request
     *
     * @return JsonResponse
     * @throws PlanningExistsException
     */
    public function createPlanning($chefId, PlanningCreateRequest $request)
    {
        $chef = User::where('role', User::ROLE_CHEF)->findOrFail($chefId);

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
        $chef = User::where('role', User::ROLE_CHEF)->findOrFail($chefId);

        $monthToDuplicate = $request->input('month_to_duplicate');
        $monthCount = $request->input('month_count');

        $planningRepository = new UserRepository($chef);

        $planning = $planningRepository->duplicatePlanning($monthToDuplicate, $monthCount);

        return response()->json(['status' => 'success', 'data' => $planning], 200);
    }
}
