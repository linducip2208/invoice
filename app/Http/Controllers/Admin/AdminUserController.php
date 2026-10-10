<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount('workspaces');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load('workspaces.subscription.plan');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isSuperadmin()) {
            return back()->with('error', 'Superadmin tidak boleh dihapus dari sini.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User dihapus.');
    }
}
