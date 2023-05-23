<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CommentRequest;
use App\Models\Alert;
use App\Models\User;
use App\Repositories\AlertRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlertController extends Controller
{
    /**
     * @param $chefId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAlertsByChef($chefId, Request $request)
    {
        $chef = User::where('role', User::ROLE_CHEF)->findOrFail($chefId);

        $alerts = AlertRepository::getAlertsByGuards($chefId);

        return response()->json(['status' => 'success', 'data' => $alerts], 200);
    }

    /**
     * @param int $superChefId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAlertsBySuperChef($superChefId, Request $request)
    {

        $superChef = User::where('role', User::ROLE_SUPER_CHEF)->findOrFail($superChefId);

        $alerts = AlertRepository::getAlertsByChefs($superChefId);

        return response()->json(['status' => 'success', 'data' => $alerts], 200);
    }

}
