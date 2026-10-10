<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ai\Features\DescriptionGenerator;
use Illuminate\Http\Request;

class AiDescriptionController extends Controller
{
    public function expand(Request $request, DescriptionGenerator $generator)
    {
        $request->validate([
            'keyword'  => 'required|string|min:2|max:200',
            'industry' => 'nullable|string|max:100',
        ]);

        try {
            $result = $generator->expand($request->keyword, $request->industry);
            return response()->json(['ok' => true, 'description' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
