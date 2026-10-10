<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $plans = Plan::where('is_public', true)->orderBy('sort_order')->take(3)->get();

        return view('welcome', compact('plans'));
    }
}
