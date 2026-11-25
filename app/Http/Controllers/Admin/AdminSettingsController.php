<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        $checks = [
            'APP_URL' => config('app.url'),
            'APP_ENV' => config('app.env'),
            'MAIL_MAILER' => config('mail.default'),
            'QUEUE_CONNECTION' => config('queue.default'),
            'Branding' => branding('app_name') . ' / ' . (branding('support_email') ?? 'no support email'),
        ];

        return view('admin.settings', compact('checks'));
    }
}
