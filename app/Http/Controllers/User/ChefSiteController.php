<?php

namespace App\Http\Controllers\User;

use App\Models\Site;
use App\Repositories\SiteRepository;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChefSiteController extends Controller
{

    /**
     *
     * @return JsonResponse
     */
    public function show()
    {
        $chef = auth()->user();
        $superChef = $chef->superChef;
        $site = null;

        if ($superChef) {
            $site = Site::where('super_chef_id', $superChef->id)->with('superChef')->first();
        }

        return response()->json(['status' => 'success', 'data' => $site], 200);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getZones(Request $request)
    {
        $chef = auth()->user();

        $page = $request->input('page', null);
        $search = $request->input('search', null);
        $perPage = $request->input('perPage', null);
        $orderBy = $request->input('orderBy', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');

        $siteRepository = new UserRepository($chef);
        $zones = $siteRepository->paginateZones($page, $perPage, $search, $orderBy, $orderDirection);

        return response()->json(['status' => 'success', 'data' => $zones], 200);
    }


}
