<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Services\Approval\ApprovalEngine;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $pending = ApprovalRequest::with('rule', 'requester')
            ->where('status', 'pending')
            ->whereHas('rule', function ($q) use ($userId) {
                $q->whereJsonContains('approver_user_ids', $userId);
            })
            ->latest()
            ->get();

        $past = ApprovalRequest::with('rule', 'requester')
            ->whereIn('status', ['approved', 'rejected', 'expired'])
            ->latest('final_decision_at')
            ->limit(20)
            ->get();

        return view('approvals.index', compact('pending', 'past'));
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest, ApprovalEngine $engine)
    {
        $request->validate(['comment' => 'nullable|string|max:500']);
        try {
            $engine->recordResponse($approvalRequest, auth()->id(), 'approved', $request->comment);
            return back()->with('success', '✓ Request di-approve.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest, ApprovalEngine $engine)
    {
        $request->validate(['comment' => 'required|string|max:500']);
        try {
            $engine->recordResponse($approvalRequest, auth()->id(), 'rejected', $request->comment);
            return back()->with('success', 'Request di-reject.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
