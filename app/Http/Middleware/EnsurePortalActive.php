<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePortalActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('client_portal')->user();
        // Revocation takes effect immediately, even mid-session.
        if ($user && ! $user->is_active) {
            Auth::guard('client_portal')->logout();
            $request->session()->invalidate();
            return redirect()->route('portal.login')
                ->with('error', __('Your portal access was revoked. Contact the business.'));
        }
        return $next($request);
    }
}
