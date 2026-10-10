<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quote;

class PortalDashboardController extends Controller
{
    public function dashboard()
    {
        $portalUser = auth('client_portal')->user();
        $client = $portalUser->client;

        $invoices = Invoice::withoutGlobalScope('workspace')
            ->where('client_id', $client->id)
            ->orderBy('issue_date', 'desc')
            ->get();

        $stats = [
            'total_outstanding' => (float) $invoices->whereIn('status', ['sent','partial','overdue'])->sum('balance_due'),
            'total_paid'        => (float) $invoices->sum('amount_paid'),
            'invoice_count'     => $invoices->count(),
            'unpaid_count'      => $invoices->whereIn('status', ['sent','partial','overdue'])->count(),
        ];

        return view('portal.dashboard', compact('client', 'invoices', 'stats', 'portalUser'));
    }

    public function invoices()
    {
        $portalUser = auth('client_portal')->user();
        $invoices = Invoice::withoutGlobalScope('workspace')
            ->where('client_id', $portalUser->client_id)
            ->orderBy('issue_date', 'desc')
            ->paginate(15);

        return view('portal.invoices', compact('invoices', 'portalUser'));
    }

    public function quotes()
    {
        $portalUser = auth('client_portal')->user();
        $quotes = Quote::withoutGlobalScope('workspace')
            ->where('client_id', $portalUser->client_id)
            ->orderBy('issue_date', 'desc')
            ->paginate(15);

        return view('portal.quotes', compact('quotes', 'portalUser'));
    }
}
