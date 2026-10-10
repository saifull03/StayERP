<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $activeBranchId = $request->attributes->get('active_branch_id');

        foreach ($permissions as $perm) {
            if ($user->hasPermission($perm, $activeBranchId)) {
                return $next($request);
            }
        }

        abort(403, "Access Denied. You lack the required permission in StayingHotel ERP.");
    }
}
