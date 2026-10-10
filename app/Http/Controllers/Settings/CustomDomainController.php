<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomDomain;
use Illuminate\Http\Request;

class CustomDomainController extends Controller
{
    public function show()
    {
        $domain = CustomDomain::first();
        return view('settings.custom-domain', compact('domain'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostname' => ['required', 'regex:/^([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,}$/i', 'max:253'],
            'verification_method' => 'required|in:cname,txt',
        ]);

        $existing = CustomDomain::withoutGlobalScope('workspace')
            ->where('hostname', strtolower($validated['hostname']))->first();

        if ($existing) {
            return back()->withErrors(['hostname' => 'Domain ini sudah dipakai.'])->withInput();
        }

        // Delete old domain (1 per workspace)
        CustomDomain::query()->delete();

        CustomDomain::create([
            'hostname'            => strtolower($validated['hostname']),
            'verification_method' => $validated['verification_method'],
            'status'              => 'pending',
        ]);

        return back()->with('success', 'Domain ditambahkan. Setup DNS lalu klik Verify.');
    }

    public function verify(Request $request)
    {
        $domain = CustomDomain::firstOrFail();
        $hostname = $domain->hostname;

        $verified = false;
        $reason = null;

        try {
            if ($domain->verification_method === 'cname') {
                $records = @dns_get_record($hostname, DNS_CNAME);
                $expected = config('app.cname_target', 'cname.invocely.app');
                foreach ($records as $r) {
                    if (isset($r['target']) && str_contains($r['target'], $expected)) {
                        $verified = true;
                        break;
                    }
                }
                if (! $verified) $reason = "CNAME tidak menunjuk ke {$expected}";
            } else {
                $records = @dns_get_record('_invocely-verify.' . $hostname, DNS_TXT);
                foreach ($records as $r) {
                    if (isset($r['txt']) && str_contains($r['txt'], $domain->verification_token)) {
                        $verified = true;
                        break;
                    }
                }
                if (! $verified) $reason = 'TXT record tidak ditemukan atau token tidak cocok';
            }
        } catch (\Throwable $e) {
            $reason = 'Error DNS lookup: ' . $e->getMessage();
        }

        $domain->update([
            'last_check_at' => now(),
            'status'        => $verified ? 'verified' : 'failed',
            'verified_at'   => $verified ? now() : null,
        ]);

        return back()->with($verified ? 'success' : 'error',
            $verified
                ? "✓ Domain {$hostname} terverifikasi."
                : "Verifikasi gagal: {$reason}"
        );
    }

    public function destroy()
    {
        CustomDomain::query()->delete();
        return back()->with('success', 'Custom domain dihapus.');
    }
}
