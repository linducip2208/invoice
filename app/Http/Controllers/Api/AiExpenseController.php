<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ai\Features\ExpenseCategorizer;
use Illuminate\Http\Request;

class AiExpenseController extends Controller
{
    public function categorize(Request $request, ExpenseCategorizer $categorizer)
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'vendor'      => 'nullable|string|max:120',
            'amount'      => 'nullable|numeric|min:0',
        ]);

        $result = $categorizer->suggest($request->description, $request->vendor, $request->amount);
        return response()->json(['ok' => true, ...$result]);
    }
}
