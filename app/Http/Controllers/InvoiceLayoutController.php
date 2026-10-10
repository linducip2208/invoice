<?php

namespace App\Http\Controllers;

use App\Models\InvoiceLayout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceLayoutController extends Controller
{
    public function index(): View
    {
        $layouts = InvoiceLayout::latest()->paginate(15);

        return view('invoice-layouts.index', compact('layouts'));
    }

    public function create(): View
    {
        // Pass concrete arrays, not Eloquent attribute access — avoids any
        // cast/accessor weirdness for unsaved models.
        $layout = (object) [
            'id'          => null,
            'name'        => 'Layout Baru',
            'description' => null,
            'blocks'      => InvoiceLayout::defaultBlocks(),
            'settings'    => ['paper' => 'A4', 'font' => 'Inter', 'primary' => '#A78BFA'],
            'is_default'  => false,
        ];

        return view('invoice-layouts.builder', compact('layout'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        $layout = InvoiceLayout::create($validated);

        return redirect()->route('invoice-layouts.edit', $layout)
            ->with('success', __('Layout dibuat.'));
    }

    public function edit(InvoiceLayout $invoiceLayout): View
    {
        return view('invoice-layouts.builder', ['layout' => $invoiceLayout]);
    }

    public function update(Request $request, InvoiceLayout $invoiceLayout): RedirectResponse
    {
        $validated = $this->validateData($request);

        $invoiceLayout->update($validated);

        return back()->with('success', __('Layout disimpan.'));
    }

    public function destroy(InvoiceLayout $invoiceLayout): RedirectResponse
    {
        $invoiceLayout->delete();

        return redirect()->route('invoice-layouts.index')->with('success', __('Layout dihapus.'));
    }

    protected function validateData(Request $request): array
    {
        // Builder posts blocks/settings as JSON-stringified hidden fields.
        // Decode them before validation so the array rules apply.
        if (is_string($request->input('blocks'))) {
            $request->merge(['blocks' => json_decode($request->input('blocks'), true) ?: []]);
        }
        if (is_string($request->input('settings'))) {
            $request->merge(['settings' => json_decode($request->input('settings'), true) ?: []]);
        }

        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string|max:500',
            'blocks'         => 'required|array|min:1',
            'blocks.*.type'  => 'required|string|max:50',
            'blocks.*.props' => 'nullable|array',
            'settings'       => 'nullable|array',
            'is_default'     => 'boolean',
        ]);

        $data['is_default'] = ! empty($data['is_default']);

        return $data;
    }
}
