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
        $portalUsers = ClientPortalUser::where('client_id', $client->id)->get();
        return view('clients.portal-users', compact('client', 'portalUsers'));
    }

    public function invite(Request $request, Client $client)
    {
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
        $portalUser->delete();
        return back()->with('success', 'Portal user dihapus.');
    }
}
