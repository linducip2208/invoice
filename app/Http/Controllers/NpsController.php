<?php

namespace App\Http\Controllers;

use App\Models\NpsResponse;
use Illuminate\Http\Request;

class NpsController extends Controller
{
    public function show(string $token)
    {
        $nps = NpsResponse::withoutGlobalScope('workspace')->where('public_token', $token)->firstOrFail();

        if ($nps->submitted_at) {
            return view('public.nps.thanks', ['nps' => $nps]);
        }

        return view('public.nps.show', compact('nps', 'token'));
    }

    public function submit(Request $request, string $token)
    {
        $validated = $request->validate([
            'score'   => 'required|integer|min:0|max:10',
            'comment' => 'nullable|string|max:2000',
        ]);

        $nps = NpsResponse::withoutGlobalScope('workspace')->where('public_token', $token)->firstOrFail();

        if ($nps->submitted_at) {
            return view('public.nps.thanks', ['nps' => $nps]);
        }

        $nps->update([
            'score'        => $validated['score'],
            'comment'      => $validated['comment'] ?? null,
            'submitted_at' => now(),
            'ip'           => $request->ip(),
            'ua'           => substr($request->userAgent() ?? '', 0, 500),
        ]);

        return view('public.nps.thanks', ['nps' => $nps]);
    }
}
