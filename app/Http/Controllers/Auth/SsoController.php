<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ExternalOauthIdentity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SsoController extends Controller
{
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'microsoft']), 404);
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        abort_unless(in_array($provider, ['google', 'microsoft']), 404);

        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'SSO gagal: ' . $e->getMessage());
        }

        $identity = ExternalOauthIdentity::where('provider', $provider)
            ->where('provider_user_id', $oauthUser->getId())
            ->first();

        if ($identity) {
            $user = $identity->user;
        } else {
            $user = User::where('email', $oauthUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name'     => $oauthUser->getName() ?: 'User',
                    'email'    => $oauthUser->getEmail(),
                    'password' => bcrypt(Str::random(40)),
                    'email_verified_at' => now(),
                ]);
            }

            ExternalOauthIdentity::create([
                'user_id'          => $user->id,
                'provider'         => $provider,
                'provider_user_id' => $oauthUser->getId(),
                'email'            => $oauthUser->getEmail(),
                'name'             => $oauthUser->getName(),
                'avatar_url'       => $oauthUser->getAvatar(),
                'token'            => $oauthUser->token ?? null,
                'linked_at'        => now(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function disconnect(Request $request, string $provider)
    {
        ExternalOauthIdentity::where('user_id', $request->user()->id)
            ->where('provider', $provider)
            ->delete();
        return back()->with('success', "Disconnect {$provider} sukses.");
    }
}
