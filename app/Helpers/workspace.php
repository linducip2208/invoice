<?php

use App\Models\Workspace;

if (! function_exists('workspace')) {
    function workspace(): ?Workspace
    {
        if (app()->runningInConsole()) {
            return null;
        }

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
