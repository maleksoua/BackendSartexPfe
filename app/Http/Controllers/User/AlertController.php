<?php

namespace App\Http\Controllers\User;

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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getGuardsAlerts(Request $request)
    {
        $chef = auth()->user();

        $alerts = AlertRepository::getAlertsByGuards($chef->id);

        return response()->json(['status' => 'success', 'data' => $alerts], 200);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getChefsAlerts(Request $request)
    {
        $superChef = auth()->user();

        $alerts = AlertRepository::getAlertsByChefs($superChef->id);

        return response()->json(['status' => 'success', 'data' => $alerts], 200);
    }


    /**
     * @param int $alertId
     * @param CommentRequest $request
     *
     * @return JsonResponse
     */
    public function addComment($alertId, CommentRequest $request)
    {
        $message = $request->input('comment');

        $chef = auth()->id();
        $alert = Alert::where('chef_id', '=', $chef)->findOrFail($alertId);

        $alertRepo = new AlertRepository($alert);

        $comment = $alertRepo->addComment($message);

        return response()->json(['status' => 'success', 'data' => $comment], 200);
    }

    /**
     * @param int $chefId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAlertsByChef($chefId, Request $request)
    {
        $superChef = auth()->id();

        $chef = User::where('role', User::ROLE_CHEF)->where('super_chef_id', $superChef)->findOrFail($chefId);

        $alerts = AlertRepository::getAlertsByGuards($chefId);

        return response()->json(['status' => 'success', 'data' => $alerts], 200);
    }

}
