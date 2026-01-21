<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user');

        // Filter by entity type
        if ($entityType = $request->input('entity_type')) {
            $query->where('entity_type', $entityType);
        }

        // Filter by action
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Filter by user
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter by date range
        if ($from = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        // Get distinct values for filters
        $entityTypes = AuditLog::distinct()->pluck('entity_type');
        $actions = AuditLog::distinct()->pluck('action');

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'entityTypes' => $entityTypes,
            'actions' => $actions,
            'filters' => $request->only(['entity_type', 'action', 'user_id', 'from_date', 'to_date']),
        ]);
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user');

        return view('admin.audit-logs.show', [
            'log' => $auditLog,
        ]);
    }
}
