<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with(['user', 'workspace'])->latest();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('action', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"));
        }
        $logs = $query->paginate(25)->withQueryString();

        return view('admin.audit-logs', compact('logs'));
    }
}
