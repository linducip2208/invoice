<?php

use App\Models\Workspace;

if (! function_exists('workspace')) {
    function workspace(): ?Workspace
    {
        // NOTE: no runningInConsole() early-return here. In real console
        // (queue/scheduler) there is no session and no auth user, so the
        // lookups below still return null. Returning null unconditionally in
        // console breaks feature tests (which run in console but issue HTTP
        // requests with session + authenticated user) and disables the
        // BelongsToWorkspace global scope exactly when it is needed.
        $workspaceId = session('current_workspace_id');

        if (! $workspaceId) {
            $user = auth()->user();
            if ($user) {
                $workspace = $user->workspaces()->first();
                if ($workspace) {
                    session(['current_workspace_id' => $workspace->id]);
                    $workspaceId = $workspace->id;
                }
            }
        }

        if ($workspaceId) {
            return app()->has(Workspace::class . ':' . $workspaceId)
                ? app(Workspace::class . ':' . $workspaceId)
                : app()->instance(
                    Workspace::class . ':' . $workspaceId,
                    Workspace::find($workspaceId)
                );
        }

        return null;
    }
}
