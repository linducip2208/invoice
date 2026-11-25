<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\CurrencyRateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $fx = [
            'mode' => CurrencyRateService::mode(),
            'manual_usd_idr' => AppSetting::all_cached()['fx.manual_USD_IDR'] ?? null,
            'live_rate' => CurrencyRateService::rate('USD', 'IDR'),
            'live_date' => CurrencyRateService::rateDate('USD', 'IDR'),
            'source' => CurrencyRateService::source('USD', 'IDR'),
        ];

        return view('admin.settings', compact('checks', 'fx'));
    }

    public function updateFx(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => 'required|in:auto,manual',
            'manual_usd_idr' => 'nullable|numeric|min:1|max:1000000',
        ]);

        AppSetting::updateOrCreate(['key' => 'fx.mode'], ['value' => $validated['mode'], 'group' => 'fx']);
        AppSetting::updateOrCreate(['key' => 'fx.manual_USD_IDR'], ['value' => $validated['manual_usd_idr'] ?? null, 'group' => 'fx']);

        return back()->with('success', __('Pengaturan kurs disimpan.'));
    }

    public function refreshFx(): RedirectResponse
    {
        Cache::forget('fx:USD:IDR');
        $rate = CurrencyRateService::rate('USD', 'IDR');

        return back()->with(
            $rate ? 'success' : 'error',
            $rate ? __('Kurs diperbarui: 1 USD = Rp :r', ['r' => number_format($rate, 0, ',', '.')]) : __('Gagal ambil kurs live. Cek koneksi / pakai mode manual.')
        );
    }
}
