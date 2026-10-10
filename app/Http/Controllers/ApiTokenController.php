<?php

namespace App\Http\Controllers;

use App\Models\WorkspaceApiToken;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = WorkspaceApiToken::where('workspace_id', workspace()->id)->latest()->get();
        $newToken = session('new_api_token');
        return view('api-tokens.index', compact('tokens', 'newToken'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:100']);

        $result = WorkspaceApiToken::generate(
            workspace()->id,
            auth()->id(),
            $validated['name']
        );

        return back()->with('new_api_token', $result['token'])
                     ->with('success', 'Token created. Copy now — won\'t be shown again.');
    }

    public function destroy(WorkspaceApiToken $apiToken)
    {
        abort_unless($apiToken->workspace_id === workspace()->id, 403);
        $apiToken->delete();
        return back()->with('success', 'Token revoked.');
    }
}
