<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $activeBranchId = $request->attributes->get('active_branch_id');

        if (!$user->hasRole($roles, $activeBranchId)) {
            abort(403, 'Unauthorized. Your role does not grant access to this section in StayingHotel ERP.');
        }

        return $next($request);
    }
}
