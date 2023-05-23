<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Cache;
use JWTAuth;

class LoginController extends Controller
{

// USER LOGIN API - POST
    public function login(LoginRequest $request)
    {
        // verify user + token
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => __('wrong_credentials')], 401);

        }
        $user = auth('api')->user();
        // send response
        return response()->json([
            'status' => 'success',
            'data' => $user,
            "access_token" => $token
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        Cache::put($token, true, config('jwt.ttl'));

        return response()->json(['status' => 'success'], 200);
    }
}

