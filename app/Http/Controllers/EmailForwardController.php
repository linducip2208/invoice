<?php

namespace App\Http\Controllers;

use App\Models\EmailInboxForward;

class EmailForwardController extends Controller
{
    public function index()
    {
        $forwards = EmailInboxForward::with('expense')->latest('forwarded_at')->paginate(20);
        $inboundAddress = 'expenses+ws' . workspace()?->id . '@invocely.app';

        return view('email-forwards.index', compact('forwards', 'inboundAddress'));
    }
}
