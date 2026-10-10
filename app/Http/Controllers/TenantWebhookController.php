<?php

namespace App\Http\Controllers;

use App\Models\TenantWebhook;
use Illuminate\Http\Request;

class TenantWebhookController extends Controller
{
    public function index()
    {
        $webhooks = TenantWebhook::latest()->get();
        return view('tenant-webhooks.index', compact('webhooks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url'      => 'required|url|max:500',
            'events'   => 'required|array|min:1',
            'events.*' => 'string|in:invoice.created,invoice.sent,invoice.paid,invoice.viewed,payment.received,client.created',
        ]);

        TenantWebhook::create($validated);
        return back()->with('success', 'Webhook added.');
    }

    public function toggle(TenantWebhook $tenantWebhook)
    {
        abort_unless($tenantWebhook->workspace_id === workspace()->id, 403);
        $tenantWebhook->update(['is_active' => ! $tenantWebhook->is_active]);
        return back();
    }

    public function destroy(TenantWebhook $tenantWebhook)
    {
        abort_unless($tenantWebhook->workspace_id === workspace()->id, 403);
        $tenantWebhook->delete();
        return back()->with('success', 'Webhook deleted.');
    }
}
