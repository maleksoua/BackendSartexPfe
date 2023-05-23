<?php

namespace App\Http\Controllers\User;

use App\Exceptions\DeletionException;
use App\Exceptions\UnableToSaveFileException;
use App\Http\Requests\GuardCreateRequest;
use App\Http\Requests\GuardUpdateRequest;
use App\Models\Guard;
use App\Repositories\GuardRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GuardController extends Controller
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

        $guards = GuardRepository::paginate($page, $perPage, $search, $orderBy, $orderDirection);

        return response()->json(['status' => 'success', 'data' => $guards], 200);
    }

    /**
     * @param int $guardId
     *
     * @return JsonResponse
     */
    public function show($guardId)
    {
        $guard = Guard::with(['chef'])->findOrFail($guardId);

        return response()->json(['status' => 'success', 'data' => $guard], 200);
    }

    /**
     * @param GuardCreateRequest $request
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     */
    public function create(GuardCreateRequest $request)
    {
        $phone = $request->input('phone');
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $register = $request->input('register_number');
        $tag = $request->input('tag');
        $chef = $request->input('chef_id');

     /*   $profileFile = $request->file('profile_image');
        $filename = '';
        if ($profileFile) {
            $filename = date('YmdHi') . $profileFile->getClientOriginalName();
            $profileFile->move(public_path('public/Image'), $filename);
        }
        if (!$filename) {
            throw new UnableToSaveFileException();
        }

        $guard = GuardRepository::create($filename, $firstName, $lastName, $register, $phone, $tag, $chef);
*/
$profileImageFile = $request->file('profile_image', null);
$filename = '';
if (isset($profileImageFile)) {
    $filename = date('YmdHi') . $profileImageFile->getClientOriginalName();
    $profileImageFile->move(public_path('public/Image'), $filename);
    if (!$filename) {
        throw new UnableToSaveFileException();
    }
}


$guardRepository->update($firstName, $lastName, $filename, $phone, $register, $tag, $chef);
        return response()->json(['status' => 'success', 'data' => $guard], 200);
    }

    /**
     * @param int $guardId
     * @param GuardUpdateRequest $request
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     */
    public function update($guardId, GuardUpdateRequest $request)
    {

        $guard = Guard::findOrFail($guardId);
        $guardRepository = new GuardRepository($guard);

        $phone = $request->input('phone', null);
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $register = $request->input('register_number');
        $tag = $request->input('tag');
        $chef = $request->input('chef_id');

        $profileImageFile = $request->file('profile_image', null);
        $filename = '';
        if (isset($profileImageFile)) {
            $filename = date('YmdHi') . $profileImageFile->getClientOriginalName();
            $profileImageFile->move(public_path('public/Image'), $filename);
            if (!$filename) {
                throw new UnableToSaveFileException();
            }
        }


        $guardRepository->update($firstName, $lastName, $filename, $phone, $register, $tag, $chef);

        return response()->json(['status' => 'success', 'data' => $guard], 200);
    }

    /**
     * @param int $guardId
     *
     * @return JsonResponse
     */
    public function delete($guardId)
    {
        $guard = Guard::findOrFail($guardId);
        $guardRepository = new GuardRepository($guard);

        try {
            $guardRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param $guardId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getEquipmentHistory($guardId, Request $request)
    {

        $guard = Guard::findOrFail($guardId);
        $guardRepository = new GuardRepository($guard);

        $page = $request->input('page', null);
        $search = $request->input('search', null);
        $perPage = $request->input('perPage', null);
        $orderBy = $request->input('orderBy', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');

        $guards = $guardRepository->paginateEquipmentHistory($page, $perPage, $search, $orderBy, $orderDirection);

        return response()->json(['status' => 'success', 'data' => $guards], 200);
    }
}
