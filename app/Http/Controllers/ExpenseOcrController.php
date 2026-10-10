<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\Ai\Features\OcrReceipt;
use Illuminate\Http\Request;

class ExpenseOcrController extends Controller
{
    public function showUpload()
    {
        return view('expenses.ocr-upload');
    }

    public function process(Request $request, OcrReceipt $ocr)
    {
        $request->validate([
            'receipt' => 'required|image|max:5120',
        ]);

        $path = $request->file('receipt')->store('receipts', 'public');

        try {
            $extracted = $ocr->extract($path);

            $expense = Expense::create([
                'workspace_id' => workspace()?->id,
                'description'  => substr($extracted['merchant_name'] ?? 'Receipt OCR', 0, 500),
                'amount'       => (float) ($extracted['total'] ?? 0),
                'currency'     => $extracted['currency'] ?? 'IDR',
                'expense_date' => $extracted['date'] ?? now()->toDateString(),
                'category'     => $extracted['category_suggested'] ?? 'Other',
                'billable'     => false,
                'receipt_path' => $path,
                'ocr_status'   => 'done',
                'ocr_extracted' => $extracted,
                'ai_category_suggested' => $extracted['category_suggested'] ?? null,
                'ai_category_confidence' => 0.85,
                'vendor_name'  => $extracted['merchant_name'] ?? null,
                'notes'        => 'Auto-extracted via OCR. Review & confirm.',
            ]);

            return redirect()->route('expenses.ocr.review', $expense)->with('success', '✓ OCR berhasil. Review hasilnya.');
        } catch (\Throwable $e) {
            // Save the file with failed status so user doesn't lose upload
            $expense = Expense::create([
                'workspace_id' => workspace()?->id,
                'description'  => 'OCR Failed - manual entry needed',
                'amount'       => 0,
                'currency'     => 'IDR',
                'expense_date' => now()->toDateString(),
                'receipt_path' => $path,
                'ocr_status'   => 'failed',
                'notes'        => 'OCR error: ' . substr($e->getMessage(), 0, 400),
            ]);
            return redirect()->route('expenses.index')->with('error', 'OCR gagal: ' . $e->getMessage());
        }
    }

    public function review(Expense $expense)
    {
        return view('expenses.ocr-review', compact('expense'));
    }

    public function confirm(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'description'  => 'required|string|max:500',
            'amount'       => 'required|numeric|min:0.01',
            'currency'     => 'required|string|size:3',
            'expense_date' => 'required|date',
            'category'     => 'nullable|string|max:60',
            'vendor_name'  => 'nullable|string|max:120',
            'billable'     => 'boolean',
            'client_id'    => 'nullable|exists:clients,id',
        ]);

        $expense->update([
            ...$validated,
            'billable' => $request->boolean('billable'),
        ]);

        return redirect()->route('expenses.index')->with('success', '✓ Expense disimpan.');
    }
}
