<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureWorkspaceExists
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Superadmin can navigate the regular app freely. If they don't have any
        // workspace yet, default to the first one available; otherwise just continue.
        if ($user?->isSuperadmin()) {
            if (! session('current_workspace_id')) {
                $first = \App\Models\Workspace::orderBy('id')->first();
                if ($first) session(['current_workspace_id' => $first->id]);
            }
            return $next($request);
        }

        if ($user && ! workspace()) {
            $workspace = $user->workspaces()->first();
            if ($workspace) {
                session(['current_workspace_id' => $workspace->id]);
            } else {
                return redirect()->route('workspace.create');
            }
        }

        return $next($request);
    }
}
