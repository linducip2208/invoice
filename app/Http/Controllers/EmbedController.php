<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class EmbedController extends Controller
{
    public function invoice(Request $request, string $token)
    {
        $invoice = Invoice::withoutGlobalScope('workspace')
            ->with('client', 'workspace', 'items')
            ->where('public_token', $token)
            ->firstOrFail();

        $response = response()->view('embed.invoice', compact('invoice'));

        // Allow iframe embedding from any origin (use CSP for finer control)
        $response->headers->remove('X-Frame-Options');
        $response->headers->set('Content-Security-Policy', "frame-ancestors *");

        return $response;
    }

    public function widgetJs(Request $request)
    {
        $response = response()->view('embed.widget-js')
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'public, max-age=3600');
        return $response;
    }

    public function demo()
    {
        return view('embed.demo');
    }
}
