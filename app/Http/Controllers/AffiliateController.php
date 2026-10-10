<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;

class AffiliateController extends Controller
{
    public function index()
    {
        $affiliate = Affiliate::forUser(auth()->user());
        return view('affiliate.index', compact('affiliate'));
    }
}
