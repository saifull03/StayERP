<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $activeBranchId = $request->attributes->get('active_branch_id');

        if (!$user->hasPermission($permission, $activeBranchId)) {
            abort(403, "Access Denied. You lack the '{$permission}' permission in StayingHotel ERP.");
        }

        return $next($request);
    }
}
