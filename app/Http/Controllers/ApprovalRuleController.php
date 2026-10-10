<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRule;
use Illuminate\Http\Request;

class ApprovalRuleController extends Controller
{
    public function index()
    {
        $rules = ApprovalRule::latest()->get();
        $users = workspace()?->users()->orderBy('name')->get() ?? collect();

        return view('approval-rules.index', compact('rules', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:120',
            'applies_to'      => 'required|in:invoice,quote,expense,credit_note',
            'condition_type'  => 'required|in:amount_gte,always',
            'condition_value' => 'nullable|numeric|min:0',
            'approver_user_ids' => 'required|array|min:1',
            'approver_user_ids.*' => 'integer|exists:users,id',
            'require_all'     => 'boolean',
        ]);

        ApprovalRule::create([
            ...$validated,
            'require_all' => $request->boolean('require_all'),
            'is_active'   => true,
        ]);

        return back()->with('success', 'Rule approval ditambahkan.');
    }

    public function update(Request $request, ApprovalRule $rule)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:120',
            'applies_to'      => 'required|in:invoice,quote,expense,credit_note',
            'condition_type'  => 'required|in:amount_gte,always',
            'condition_value' => 'nullable|numeric|min:0',
            'approver_user_ids' => 'required|array|min:1',
            'require_all'     => 'boolean',
            'is_active'       => 'boolean',
        ]);

        $rule->update([
            ...$validated,
            'require_all' => $request->boolean('require_all'),
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Rule diupdate.');
    }

    public function destroy(ApprovalRule $rule)
    {
        $rule->delete();
        return back()->with('success', 'Rule dihapus.');
    }
}
