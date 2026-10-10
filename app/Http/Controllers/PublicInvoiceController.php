<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePublicView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicInvoiceController extends Controller
{
    public function show(string $token, Request $request): View
    {
        $invoice = Invoice::where('public_token', $token)
            ->with(['items' => function ($q) {
                $q->orderBy('sort_order');
            }, 'client', 'workspace'])
            ->firstOrFail();

        InvoicePublicView::create([
            'invoice_id'  => $invoice->id,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'viewed_at'   => now(),
        ]);

        $primaryColor = $invoice->primary_color ?: $invoice->workspace->primary_color ?? '#2563EB';
        $accentColor  = $invoice->accent_color ?: $invoice->workspace->accent_color ?? '#1E40AF';

        return view('public.invoice', compact('invoice', 'primaryColor', 'accentColor'));
    }
}
