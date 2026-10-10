<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $action = $request->input('action');
        $search = $request->input('search');

        $query = AuditLog::with(['user', 'branch'])->latest('created_at');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($action) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('audit_logs.index', compact('logs', 'action', 'search'));
    }
}
