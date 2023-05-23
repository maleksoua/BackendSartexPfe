<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserRole
{
    const ROLES = [
        User::ROLE_ADMIN => 'admin',
        User::ROLE_CHEF => 'chef',
        User::ROLE_SUPER_CHEF => 'superChef',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param array ...$roles
     *
     * @return JsonResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $authUserRole = @self::ROLES[auth()->user()->role];
        if (in_array($authUserRole, $roles)) {
            return $next($request);
        }

        return response()->json(['status' => 'error', 'message' => 'UNAUTHORIZED'], 403);
    }
}

