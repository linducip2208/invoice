<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperadmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSuperadmin()) {
            abort(403, 'Hanya superadmin yang boleh akses halaman ini.');
        }

        return $next($request);
    }
}
