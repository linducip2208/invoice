<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

class PlanComparisonController extends Controller
{
    public function show(string $a, string $b): View
    {
        $planA = Plan::where('slug', $a)->firstOrFail();
        $planB = Plan::where('slug', $b)->firstOrFail();

        $featureLabels = [
            'max_invoices_lifetime'  => 'Max Invoice (selamanya)',
            'max_clients'            => 'Max Klien',
            'max_users'              => 'Max User / Team',
            'recurring_enabled'      => 'Recurring Invoice',
            'custom_branding'        => 'Custom Branding',
            'remove_watermark'       => 'No Watermark',
            'drag_drop_builder'      => 'Drag-Drop Invoice Builder',
            'api_access'             => 'REST API Access',
            'audit_log'              => 'Audit Log',
            'time_tracking'          => 'Time Tracking',
            'expense_tracking'       => 'Expense Tracking',
            'bulk_actions'           => 'Bulk Actions',
            'dunning'                => 'Auto Reminder (Dunning)',
            'custom_email_template'  => 'Custom Email Template',
            'custom_domain'          => 'Custom Domain',
            'tenant_webhooks'        => 'Outbound Webhooks',
            '2fa'                    => 'Two-Factor Auth',
            'csv_import'             => 'CSV Import',
        ];

        return view('pricing.compare', compact('planA', 'planB', 'featureLabels'));
    }
}
