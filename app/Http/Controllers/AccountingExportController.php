<?php

namespace App\Http\Controllers;

use App\Models\AccountingExport;
use App\Services\Accounting\ExportRunner;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AccountingExportController extends Controller
{
    public function index()
    {
        $exports = AccountingExport::with('generator')->latest()->paginate(15);
        return view('accounting-exports.index', compact('exports'));
    }

    public function create()
    {
        return view('accounting-exports.create');
    }

    public function store(Request $request, ExportRunner $runner)
    {
        $validated = $request->validate([
            'format'       => 'required|in:jurnal,mekari,quickbooks,xero,csv_generic',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
        ]);

        $export = $runner->run(
            $validated['format'],
            Carbon::parse($validated['period_start']),
            Carbon::parse($validated['period_end'])
        );

        return redirect()->route('accounting-exports.index')->with('success', "✓ {$export->record_count} record di-export.");
    }

    public function download(AccountingExport $export)
    {
        $absolute = Storage::disk('public')->path($export->file_path);
        return response()->download($absolute, basename($export->file_path));
    }

    public function destroy(AccountingExport $export)
    {
        if ($export->file_path) Storage::disk('public')->delete($export->file_path);
        $export->delete();
        return back()->with('success', 'Export dihapus.');
    }
}
