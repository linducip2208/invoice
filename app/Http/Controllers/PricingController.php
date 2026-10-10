<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(Request $request): View
    {
        $currency = strtoupper($request->get('currency', 'IDR')) === 'USD' ? 'USD' : 'IDR';

        $plans = Plan::where('is_public', true)->orderBy('sort_order')->get();

        return view('pricing.index', compact('plans', 'currency'));
    }
}
