<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('client')->latest('expense_date');
        if ($request->filled('category')) $query->where('category', $request->category);

        $expenses = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('name')->get();
        $totalAmount = Expense::sum('amount');
        $categories = Expense::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('expenses.index', compact('expenses', 'clients', 'totalAmount', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'    => 'nullable|exists:clients,id',
            'category'     => 'nullable|string|max:100',
            'description'  => 'required|string|max:500',
            'amount'       => 'required|numeric|min:0',
            'currency'     => 'required|in:IDR,USD',
            'expense_date' => 'required|date',
            'billable'     => 'boolean',
            'receipt'      => 'nullable|image|max:2048',
            'notes'        => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expenses', 'public');
        }
        $validated['billable'] = ! empty($validated['billable']);
        unset($validated['receipt']);

        Expense::create($validated);
        return back()->with('success', 'Expense recorded.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) Storage::disk('public')->delete($expense->receipt_path);
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
