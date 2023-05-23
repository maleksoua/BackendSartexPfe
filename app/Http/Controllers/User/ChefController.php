<?php

namespace App\Http\Controllers\User;

use App\Exceptions\DeletionException;
use App\Exceptions\EmailServiceNotAvailableException;
use App\Exceptions\UnableToSaveFileException;
use App\Http\Requests\ChefCreateRequest;
use App\Http\Requests\ChefUpdateRequest;
use App\Models\User;
use App\Repositories\MailRepository;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChefController extends Controller
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

        $superChef = auth()->id();

        $chefs = UserRepository::paginate($page, $perPage, $search, $orderBy, $orderDirection, User::ROLE_CHEF, $superChef);

        return response()->json(['status' => 'success', 'data' => $chefs], 200);
    }

    /**
     * @param int $chefId
     *
     * @return JsonResponse
     */
    public function show($chefId)
    {
        $superChef = auth()->id();

        $chef = User::where('role', User::ROLE_CHEF)->where('super_chef_id', $superChef)->findOrFail($chefId);

        return response()->json(['status' => 'success', 'data' => $chef], 200);
    }

    /**
     * @param ChefCreateRequest $request
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     * @throws EmailServiceNotAvailableException
     */
    public function create(ChefCreateRequest $request)
    {
        $phone = $request->input('phone');
        $email = $request->input('email');
        $password = $request->input('password');
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $register = $request->input('register_number');
        $sendEmail = (bool)$request->input('send_email', null);
        $site = $request->input('site');

        $profileFile = $request->file('profile_image');
        $filename = '';
        if ($profileFile) {
            $filename = date('YmdHi') . $profileFile->getClientOriginalName();
            $profileFile->move(public_path('public/Image'), $filename);
        }
        if (!$filename) {
            throw new UnableToSaveFileException();
        }

        $superChef = auth()->id();

        $chef = UserRepository::create($filename, $firstName, $lastName, $email, User::ROLE_CHEF, $password, $register, $phone, $superChef, $site);
        if ($sendEmail) {
            MailRepository::sendPasswordCreateMail($chef, $password);
        }

        return response()->json(['status' => 'success', 'data' => $chef], 200);
    }

    /**
     * @param int $chefId
     * @param ChefUpdateRequest $request
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     */
    public function update($chefId, ChefUpdateRequest $request)
    {
        $superChef = auth()->id();

        $chef = User::where('role', User::ROLE_CHEF)->where('super_chef_id', $superChef)->findOrFail($chefId);
        $chefRepository = new UserRepository($chef);

        $email = $request->input('email');
        $phone = $request->input('phone', null);
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $register = $request->input('register_number');
        $password = $request->input('password');
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


        $chefRepository->update($firstName, $lastName, $email, $filename, $phone, $password, $register, $superChef, $site, $zoneIds, $chefs);

        return response()->json(['status' => 'success', 'data' => $chef], 200);
    }

    /**
     * @param int $chefId
     *
     * @return JsonResponse
     */
    public function delete($chefId)
    {
        $superChef = auth()->id();

        $chef = User::where('role', User::ROLE_CHEF)->where('super_chef_id', $superChef)->findOrFail($chefId);
        $chefRepository = new UserRepository($chef);

        try {
            $chefRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
