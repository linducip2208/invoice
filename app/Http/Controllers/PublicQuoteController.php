<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Services\Signature\SignatureCapture;
use Illuminate\Http\Request;

class PublicQuoteController extends Controller
{
    public function show(string $token)
    {
        $quote = Quote::withoutGlobalScope('workspace')
            ->with('client', 'workspace')
            ->where('public_token', $token)
            ->firstOrFail();

        $signatures = $quote->morphMany(\App\Models\Signature::class, 'subject')->get();
        $isSigned = $signatures->isNotEmpty();

        return view('public.quotes.show', compact('quote', 'signatures', 'isSigned'));
    }

    public function accept(string $token)
    {
        $quote = Quote::withoutGlobalScope('workspace')->where('public_token', $token)->firstOrFail();
        if (! in_array($quote->status, ['draft', 'sent'])) {
            return back()->with('error', 'Quote tidak bisa di-accept di status ' . $quote->status);
        }
        $quote->update(['status' => 'accepted', 'accepted_at' => now()]);
        return redirect()->route('public.quote.show', $token)->with('success', '✓ Quote diterima!');
    }

    public function reject(string $token)
    {
        $quote = Quote::withoutGlobalScope('workspace')->where('public_token', $token)->firstOrFail();
        $quote->update(['status' => 'rejected']);
        return redirect()->route('public.quote.show', $token)->with('success', 'Quote ditolak.');
    }

    public function sign(string $token, Request $request, SignatureCapture $capture)
    {
        $validated = $request->validate([
            'signer_name'      => 'required|string|max:120',
            'signer_email'     => 'required|email|max:255',
            'signature_data'   => 'nullable|string',
            'signature_typed'  => 'nullable|string|max:120',
        ]);

        if (empty($validated['signature_data']) && empty($validated['signature_typed'])) {
            return back()->withErrors(['signature_data' => 'Tanda tangan diperlukan (gambar atau ketik nama).'])->withInput();
        }

        $quote = Quote::withoutGlobalScope('workspace')->where('public_token', $token)->firstOrFail();

        if ($quote->status === 'rejected' || $quote->status === 'expired') {
            return back()->with('error', 'Quote tidak aktif lagi.');
        }

        $capture->capture(
            subject: $quote,
            signerName: $validated['signer_name'],
            signerEmail: $validated['signer_email'],
            signatureDataUrl: $validated['signature_data'] ?? null,
            signatureTyped: $validated['signature_typed'] ?? null,
            signerRole: 'client',
        );

        $quote->update([
            'status'      => 'accepted',
            'accepted_at' => $quote->accepted_at ?? now(),
            'signed_at'   => now(),
        ]);

        return redirect()->route('public.quote.show', $token)->with('success', '✓ Quote ditandatangani!');
    }
}
