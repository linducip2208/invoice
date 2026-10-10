<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ComingSoonController extends Controller
{
    public function index(Request $request, string $feature): View
    {
        $features = [
            'audit-log'         => ['icon' => '📜', 'name' => 'Audit Log', 'desc' => 'Track semua perubahan workspace.', 'plan' => 'Pro+'],
            'time-tracking'     => ['icon' => '⏱️', 'name' => 'Time Tracking', 'desc' => 'Catat waktu kerja, convert ke invoice.', 'plan' => 'Pro+'],
            'expenses'          => ['icon' => '🧾', 'name' => 'Expense Tracking', 'desc' => 'Catat pengeluaran bisnis, profit report.', 'plan' => 'Pro+'],
            'quotes'            => ['icon' => '📋', 'name' => 'Quotes / Estimates', 'desc' => 'Kirim quote, auto-convert ke invoice saat di-accept.', 'plan' => 'Pro+'],
            'notifications'     => ['icon' => '🔔', 'name' => 'Notifications', 'desc' => 'Bell icon — invoice paid, viewed, dll.', 'plan' => 'All'],
            'team'              => ['icon' => '👥', 'name' => 'Team Members', 'desc' => 'Invite teammates dengan role.', 'plan' => 'Pro+ (3 user) / Business (∞)'],
            'client-portal'     => ['icon' => '🪟', 'name' => 'Client Portal', 'desc' => 'Magic-link login untuk client lihat semua invoice.', 'plan' => 'All'],
            'csv-import'        => ['icon' => '📥', 'name' => 'CSV Import', 'desc' => 'Bulk import client / produk dari CSV.', 'plan' => 'Pro+'],
            'bulk-actions'      => ['icon' => '📦', 'name' => 'Bulk Actions', 'desc' => 'Kirim, download, mark paid sekaligus.', 'plan' => 'Pro+'],
            'dunning'           => ['icon' => '📨', 'name' => 'Auto Reminder (Dunning)', 'desc' => 'Auto-email reminder invoice overdue.', 'plan' => 'Pro+'],
            'api'               => ['icon' => '🔌', 'name' => 'REST API', 'desc' => 'Token-based API untuk integrasi.', 'plan' => 'Business'],
            'webhooks-tenant'   => ['icon' => '🪝', 'name' => 'Outbound Webhooks', 'desc' => 'Kirim event ke URL kamu sendiri.', 'plan' => 'Business'],
            'custom-domain'     => ['icon' => '🌐', 'name' => 'Custom Domain', 'desc' => 'White-label di domain sendiri.', 'plan' => 'Business'],
            '2fa'               => ['icon' => '🔐', 'name' => 'Two-Factor Auth', 'desc' => 'TOTP via Google Authenticator.', 'plan' => 'Pro+'],
            'email-templates'   => ['icon' => '✉️', 'name' => 'Email Templates', 'desc' => 'Customize email subject & body.', 'plan' => 'Pro+'],
            'late-fee'          => ['icon' => '⏰', 'name' => 'Late Fee Auto', 'desc' => 'Tambah fee otomatis ke invoice overdue.', 'plan' => 'Pro+'],
            'tax-id'            => ['icon' => '🏛️', 'name' => 'Indonesia Tax (e-Faktur)', 'desc' => 'PPN, PPh, NPWP compliance.', 'plan' => 'Pro+'],
            'fx-rates'          => ['icon' => '💱', 'name' => 'Exchange Rates', 'desc' => 'Auto-sync USD/IDR rates daily.', 'plan' => 'All'],
            'attachments'       => ['icon' => '📎', 'name' => 'Invoice Attachments', 'desc' => 'Upload kontrak, deliverables, bukti.', 'plan' => 'All' ],
            'activity'          => ['icon' => '📰', 'name' => 'Activity Feed', 'desc' => 'Timeline event workspace.', 'plan' => 'All'],
            'exports'           => ['icon' => '📊', 'name' => 'Export CSV', 'desc' => 'Download data invoice/payment/client.', 'plan' => 'Pro+'],
            'affiliate'         => ['icon' => '🤝', 'name' => 'Affiliate Program', 'desc' => 'Referral link + komisi.', 'plan' => 'All'],
            'coupons'           => ['icon' => '🎟️', 'name' => 'Coupons', 'desc' => 'Promo code untuk subscription.', 'plan' => 'Superadmin'],
            'support'           => ['icon' => '💬', 'name' => 'Support Tickets', 'desc' => 'Submit issue ke superadmin.', 'plan' => 'All'],
            'onboarding'        => ['icon' => '🎓', 'name' => 'Onboarding Wizard', 'desc' => 'Step-by-step setup user baru.', 'plan' => 'All'],
        ];

        $info = $features[$feature] ?? ['icon' => '✨', 'name' => ucwords(str_replace('-', ' ', $feature)), 'desc' => 'Coming soon.', 'plan' => 'TBD'];

        return view('coming-soon', compact('feature', 'info'));
    }
}
