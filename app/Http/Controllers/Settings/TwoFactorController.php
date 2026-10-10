<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\UserTwoFactorSecret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $tfa = $user->twoFactorSecret;
        $isEnabled = $tfa && $tfa->enabled_at !== null;

        return view('settings.2fa.show', compact('user', 'tfa', 'isEnabled'));
    }

    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->twoFactorSecret && $user->twoFactorSecret->enabled_at) {
            return back()->with('error', '2FA sudah aktif.');
        }

        $g2fa = new Google2FA();
        $secret = $g2fa->generateSecretKey();
        $recoveryCodes = collect(range(1, 8))->map(fn () => Str::upper(Str::random(10)))->all();

        UserTwoFactorSecret::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret_encrypted' => Crypt::encryptString($secret),
                'recovery_codes_encrypted' => array_map(fn ($c) => Crypt::encryptString($c), $recoveryCodes),
                'enabled_at' => null,
            ]
        );

        $qrUrl = $g2fa->getQRCodeUrl(
            config('app.name', 'Invocely'),
            $user->email,
            $secret
        );

        return view('settings.2fa.setup', compact('secret', 'qrUrl', 'recoveryCodes'));
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = $request->user();
        $tfa = $user->twoFactorSecret;

        if (! $tfa) {
            return redirect()->route('2fa.show')->with('error', 'Setup 2FA dulu.');
        }

        $secret = Crypt::decryptString($tfa->secret_encrypted);
        $g2fa = new Google2FA();

        if (! $g2fa->verifyKey($secret, $request->code)) {
            return back()->with('error', 'Kode tidak cocok. Coba lagi.');
        }

        $tfa->update(['enabled_at' => now()]);
        $user->update(['two_factor_enabled' => true]);

        return redirect()->route('2fa.show')->with('success', '✓ 2FA aktif. Selalu butuh kode authenticator saat login.');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);

        $user = $request->user();

        if ($user->twoFactorSecret) {
            $user->twoFactorSecret->delete();
        }
        $user->update(['two_factor_enabled' => false]);

        return back()->with('success', '2FA dinonaktifkan.');
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);

        $user = $request->user();
        $tfa = $user->twoFactorSecret;

        if (! $tfa || ! $tfa->enabled_at) {
            return back()->with('error', '2FA belum aktif.');
        }

        $codes = collect(range(1, 8))->map(fn () => Str::upper(Str::random(10)))->all();
        $tfa->update([
            'recovery_codes_encrypted' => array_map(fn ($c) => Crypt::encryptString($c), $codes),
        ]);

        return back()->with('recovery_codes', $codes)->with('success', 'Recovery codes baru dibuat. Simpan baik-baik.');
    }
}
