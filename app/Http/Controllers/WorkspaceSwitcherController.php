<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;

class WorkspaceSwitcherController extends Controller
{
    public function switch(Workspace $workspace): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->workspaces()->where('workspaces.id', $workspace->id)->exists(), 403);

        session(['current_workspace_id' => $workspace->id]);

        return redirect()->route('dashboard')->with('success', 'Beralih ke workspace ' . $workspace->name);
    }
}
