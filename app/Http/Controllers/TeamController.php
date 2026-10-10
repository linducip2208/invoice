<?php

namespace App\Http\Controllers;

use App\Models\WorkspaceInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    public function index()
    {
        $workspace = workspace();
        $members = $workspace->users()->get();
        $pending = WorkspaceInvite::where('workspace_id', $workspace->id)->whereNull('accepted_at')->get();
        return view('team.index', compact('workspace', 'members', 'pending'));
    }

    public function invite(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role'  => 'required|in:admin,member',
        ]);

        $maxUsers = workspace()->planFeature('max_users');
        $currentCount = workspace()->users()->count() + WorkspaceInvite::where('workspace_id', workspace()->id)->whereNull('accepted_at')->count();

        if ($maxUsers !== null && $currentCount >= (int) $maxUsers) {
            return back()->with('error', "Plan limit: max {$maxUsers} users. Upgrade dulu.");
        }

        WorkspaceInvite::create([
            'workspace_id' => workspace()->id,
            'invited_by'   => auth()->id(),
            'email'        => $validated['email'],
            'role'         => $validated['role'],
        ]);

        return back()->with('success', 'Invite dikirim ke ' . $validated['email']);
    }

    public function revoke(WorkspaceInvite $invite)
    {
        abort_unless($invite->workspace_id === workspace()->id, 403);
        $invite->delete();
        return back()->with('success', 'Invite dibatalkan.');
    }

    public function accept(string $token)
    {
        $invite = WorkspaceInvite::where('token', $token)->firstOrFail();
        abort_if($invite->isExpired(), 410, 'Invite expired.');
        abort_if($invite->isAccepted(), 409, 'Already accepted.');

        $user = auth()->user() ?? User::firstOrCreate(
            ['email' => $invite->email],
            ['name' => Str::title(explode('@', $invite->email)[0]), 'password' => Hash::make(Str::random(16))]
        );

        if (! auth()->check()) auth()->login($user);

        $invite->workspace->users()->syncWithoutDetaching([$user->id => ['role' => $invite->role]]);
        $invite->update(['accepted_at' => now()]);
        session(['current_workspace_id' => $invite->workspace_id]);

        return redirect()->route('dashboard')->with('success', 'Bergabung di ' . $invite->workspace->name);
    }
}
