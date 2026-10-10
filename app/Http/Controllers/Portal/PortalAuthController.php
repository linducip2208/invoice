<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientPortalUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = ClientPortalUser::where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password_hash)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        Auth::guard('client_portal')->login($user, $request->boolean('remember'));
        $user->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('client_portal')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}
