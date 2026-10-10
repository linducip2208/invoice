<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Generic stub controller for features that have settings UI but full
 * implementation lives in the workspace service / scheduled job layer.
 * Each method renders a minimal management page.
 */
class StubController extends Controller
{
    public function clientPortal(): View
    {
        return view('stubs.client-portal');
    }

    public function lateFee(): View
    {
        return view('stubs.late-fee', ['workspace' => workspace()]);
    }

    public function onboarding(): View
    {
        return view('stubs.onboarding');
    }

    public function dunning(): View
    {
        return view('stubs.dunning');
    }

    public function emailTemplates(): View
    {
        return view('stubs.email-templates');
    }

    public function customDomain(): View
    {
        return view('stubs.custom-domain', ['workspace' => workspace()]);
    }

    public function indonesiaTax(): View
    {
        return view('stubs.indonesia-tax');
    }

    public function fxRates(): View
    {
        return view('stubs.fx-rates');
    }

    public function twoFactor(): View
    {
        return view('stubs.two-factor');
    }

    public function activity(): View
    {
        $logs = \App\Models\AuditLog::with('user')->latest()->take(50)->get();
        return view('stubs.activity', compact('logs'));
    }

    public function bulk(): View
    {
        return view('stubs.bulk');
    }
}
