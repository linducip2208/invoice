<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPortalUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientPortalManageController extends Controller
{
    public function show(Client $client)
    {
        abort_unless($client->workspace_id === workspace()?->id, 403);
        $portalUsers = ClientPortalUser::where('client_id', $client->id)->get();
        return view('clients.portal-users', compact('client', 'portalUsers'));
    }

    public function invite(Request $request, Client $client)
    {
        abort_unless($client->workspace_id === workspace()?->id, 403);
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $existing = ClientPortalUser::where('client_id', $client->id)
            ->where('email', $validated['email'])->first();
        if ($existing) {
            return back()->with('error', 'Email sudah ada di portal user untuk klien ini.');
        }

        $tempPassword = Str::random(12);

        ClientPortalUser::create([
            'client_id'     => $client->id,
            'workspace_id'  => $client->workspace_id,
            'email'         => $validated['email'],
            'password_hash' => Hash::make($tempPassword),
        ]);

        return back()->with('success', "✓ Portal user dibuat. Password sementara: {$tempPassword} (kirim ke klien & minta ganti).");
    }

    public function destroy(ClientPortalUser $portalUser)
    {
        abort_unless($portalUser->workspace_id === workspace()?->id, 403);
        $portalUser->delete();
        return back()->with('success', 'Portal user dihapus.');
    }

    /** Designate / undesignate a contact as quote approver. */
    public function toggleApprove(ClientPortalUser $portalUser)
    {
        abort_unless($portalUser->workspace_id === workspace()?->id, 403);
        $portalUser->update(['can_approve_quotes' => ! $portalUser->can_approve_quotes]);
        return back()->with('success', $portalUser->can_approve_quotes
            ? __(':email is now a designated quote approver.', ['email' => $portalUser->email])
            : __('Approver rights revoked for :email.', ['email' => $portalUser->email]));
    }

    /** Activate / revoke portal access. Revocation kills sessions via login gate. */
    public function toggleActive(ClientPortalUser $portalUser)
    {
        abort_unless($portalUser->workspace_id === workspace()?->id, 403);
        $portalUser->update(['is_active' => ! $portalUser->is_active]);
        return back()->with('success', $portalUser->is_active
            ? __('Access re-enabled for :email.', ['email' => $portalUser->email])
            : __('Access revoked for :email.', ['email' => $portalUser->email]));
    }
}
