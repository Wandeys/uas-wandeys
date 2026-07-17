<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action_type')) {
            $query->where('action', $request->action_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();
        
        // Get unique actions for filter
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('setting.audit_logs', [
            'title' => 'Audit Logs',
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
        ]);
    }
}
