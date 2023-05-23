<?php

namespace App\Http\Controllers\User;

use App\Exceptions\DeletionException;
use App\Exceptions\UnableToSaveFileException;
use App\Http\Requests\SiteUpdateRequest;
use App\Http\Requests\ZoneCreateRequest;
use App\Models\Site;
use App\Repositories\SiteRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
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

        $sites = SiteRepository::paginate($page, $perPage, $search, $orderBy, $orderDirection, $superChef);

        return response()->json(['status' => 'success', 'data' => $sites], 200);
    }

    /**
     * @param int $siteId
     *
     * @return JsonResponse
     */
    public function show($siteId)
    {
        $superChef = auth()->id();

        $site = Site::where('super_chef_id', $superChef)->with('superChef')->findOrFail($siteId);

        return response()->json(['status' => 'success', 'data' => $site], 200);
    }

    /**
     * @param SiteUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function create(SiteUpdateRequest $request)
    {
        $superChef = auth()->id();
        $name = $request->input('name');

        $site = SiteRepository::create($name, $superChef);

        return response()->json(['status' => 'success', 'data' => $site], 200);
    }

    /**
     * @param $siteId
     * @param SiteUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update($siteId, SiteUpdateRequest $request)
    {
        $superChef = auth()->id();

        $site = Site::where('super_chef_id', $superChef)->findOrFail($siteId);

        $name = $request->input('name');

        $siteRepository = new SiteRepository($site);

        $site = $siteRepository->update($name, $superChef);

        return response()->json(['status' => 'success', 'data' => $site], 200);
    }

    /**
     * @param int $siteId
     *
     * @return JsonResponse
     */
    public function delete($siteId)
    {
        $superChef = auth()->id();

        $site = Site::where('super_chef_id', $superChef)->findOrFail($siteId);

        $siteRepository = new SiteRepository($site);

        try {
            $siteRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }


    /**
     * @param int $siteId
     *
     * @return JsonResponse
     * @throws UnableToSaveFileException
     */
    public function addZone($siteId, ZoneCreateRequest $request)
    {
        $superChef = auth()->id();

        $site = Site::where('super_chef_id', $superChef)->findOrFail($siteId);
        $name = $request->input('name');
        $chef = $request->input('chef_id', null);

        $image = $request->file('image');
        $filename = '';
        if ($image) {
            $filename = date('YmdHi') . $image->getClientOriginalName();
            $image->move(public_path('public/Image'), $filename);
        }
        if (!$filename) {
            throw new UnableToSaveFileException();
        }

        $siteRepository = new SiteRepository($site);

        $zone = $siteRepository->addZone($name, $chef, $image);

        return response()->json(['status' => 'success', 'data' => $zone], 200);
    }

    /**
     * @param $siteId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getZones($siteId, Request $request)
    {
        $superChef = auth()->id();

        $site = Site::where('super_chef_id', $superChef)->findOrFail($siteId);

        $page = $request->input('page', null);
        $search = $request->input('search', null);
        $perPage = $request->input('perPage', null);
        $orderBy = $request->input('orderBy', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');

        $siteRepository = new SiteRepository($site);
        $zones = $siteRepository->paginateZones($page, $perPage, $search, $orderBy, $orderDirection);

        return response()->json(['status' => 'success', 'data' => $zones], 200);
    }


}
