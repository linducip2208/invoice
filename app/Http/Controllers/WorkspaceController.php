<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
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
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);

        $workspace->update($validated);

        return Redirect::route('workspace.settings')->with('status', 'workspace-updated');
    }
}
