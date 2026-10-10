<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Indonesia\NpwpValidator;
use Illuminate\Http\Request;

class NpwpValidationController extends Controller
{
    public function validate(Request $request, NpwpValidator $validator)
    {
        $request->validate(['npwp' => 'required|string|max:30']);
        $result = $validator->validate($request->npwp);
        return response()->json($result);
    }
}
