<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    /**
     * Show the form to create the user's first workspace.
     * Reachable when EnsureWorkspaceExists redirects users without any
     * workspace (route must stay OUTSIDE the `workspace` middleware group).
     */
    public function create(): View
    {
        return view('workspace.create');
    }

    /**
     * Store the user's first workspace and attach them as owner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', Rule::in(['IDR', 'USD', 'EUR', 'GBP', 'SGD', 'AUD', 'JPY', 'CNY'])],
        ]);

        $user = $request->user();

        $workspace = \App\Models\Workspace::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.strtolower(Str::random(5)),
            'currency' => $validated['currency'],
            'invoice_prefix' => 'INV-',
        ]);

        $user->workspaces()->attach($workspace->id, ['role' => 'owner']);
        session(['current_workspace_id' => $workspace->id]);

        return Redirect::route('dashboard')->with('success', __('Workspace created successfully.'));
    }

    public function edit(): View
    {
        return view('workspace.settings', [
            'workspace' => workspace(),
            'templates' => [
                'classic' => 'Classic',
                'modern' => 'Modern',
                'minimal' => 'Minimal',
                'bold' => 'Bold',
                'elegant' => 'Elegant',
            ],
            'currencies' => [
                'IDR' => 'IDR — Indonesian Rupiah',
                'USD' => 'USD — US Dollar',
                'EUR' => 'EUR — Euro',
                'GBP' => 'GBP — British Pound',
                'SGD' => 'SGD — Singapore Dollar',
                'AUD' => 'AUD — Australian Dollar',
                'JPY' => 'JPY — Japanese Yen',
                'CNY' => 'CNY — Chinese Yuan',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $workspace = workspace();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'invoice_prefix' => ['required', 'string', 'max:20'],
            'currency' => ['required', 'string', Rule::in(['IDR', 'USD', 'EUR', 'GBP', 'SGD', 'AUD', 'JPY', 'CNY'])],
            'default_template' => ['required', 'string', Rule::in(['classic', 'modern', 'minimal', 'bold', 'elegant'])],
            'primary_color' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'quote_auto_convert' => ['nullable', 'string', Rule::in(['manual', 'draft_on_accept'])],
            'quote_high_risk_threshold' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Back-compat for stale settings tabs submitted without the new fields.
        $validated['quote_auto_convert'] ??= $workspace->quote_auto_convert ?? 'manual';

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);

        $workspace->update($validated);

        return Redirect::route('workspace.settings')->with('status', 'workspace-updated');
    }
}
