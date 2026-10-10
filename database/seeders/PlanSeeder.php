<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug'              => 'free',
                'name'              => 'Free',
                'tagline'           => 'Coba dulu — gratis selamanya',
                'description'       => 'Sempurna untuk mencoba. 1 invoice tanpa expire, dengan watermark Invocely.',
                'price_idr_monthly' => 0,
                'price_idr_yearly'  => 0,
                'price_usd_monthly' => 0,
                'price_usd_yearly'  => 0,
                'trial_days'        => 0,
                'is_public'         => true,
                'is_default'        => true,
                'sort_order'        => 1,
                'features'          => [
                    'max_invoices_lifetime'  => 1,
                    'max_invoices_per_month' => null,
                    'max_clients'            => 5,
                    'max_users'              => 1,
                    'invoice_lock'           => true,
                    'recurring_enabled'      => false,
                    'custom_branding'        => false,
                    'remove_watermark'       => false,
                    'drag_drop_builder'      => false,
                    'api_access'             => false,
                    'audit_log'              => false,
                    'time_tracking'          => false,
                    'expense_tracking'       => false,
                    'bulk_actions'           => false,
                    'dunning'                => false,
                    'custom_email_template'  => false,
                    'custom_domain'          => false,
                    'tenant_webhooks'        => false,
                    '2fa'                    => false,
                    'csv_import'             => false,
                ],
            ],
            [
                'slug'              => 'pro',
                'name'              => 'Pro',
                'tagline'           => 'Untuk freelancer dan UKM yang serius',
                'description'       => 'Invoice unlimited, recurring, automation, dan branding sendiri.',
                'price_idr_monthly' => 99000,
                'price_idr_yearly'  => 990000,
                'price_usd_monthly' => 9,
                'price_usd_yearly'  => 90,
                'trial_days'        => 0,
                'is_public'         => true,
                'is_default'        => false,
                'sort_order'        => 2,
                'features'          => [
                    'max_invoices_lifetime'  => null,
                    'max_invoices_per_month' => null,
                    'max_clients'            => null,
                    'max_users'              => 3,
                    'invoice_lock'           => false,
                    'recurring_enabled'      => true,
                    'custom_branding'        => true,
                    'remove_watermark'       => true,
                    'drag_drop_builder'      => false,
                    'api_access'             => false,
                    'audit_log'              => true,
                    'time_tracking'          => true,
                    'expense_tracking'       => true,
                    'bulk_actions'           => true,
                    'dunning'                => true,
                    'custom_email_template'  => true,
                    'custom_domain'          => false,
                    'tenant_webhooks'        => false,
                    '2fa'                    => true,
                    'csv_import'             => true,
                ],
            ],
            [
                'slug'              => 'business',
                'name'              => 'Business',
                'tagline'           => 'Untuk agency dan tim yang scale',
                'description'       => 'Drag-drop invoice builder, API, custom domain, unlimited team.',
                'price_idr_monthly' => 299000,
                'price_idr_yearly'  => 2990000,
                'price_usd_monthly' => 29,
                'price_usd_yearly'  => 290,
                'trial_days'        => 0,
                'is_public'         => true,
                'is_default'        => false,
                'sort_order'        => 3,
                'features'          => [
                    'max_invoices_lifetime'  => null,
                    'max_invoices_per_month' => null,
                    'max_clients'            => null,
                    'max_users'              => null,
                    'invoice_lock'           => false,
                    'recurring_enabled'      => true,
                    'custom_branding'        => true,
                    'remove_watermark'       => true,
                    'drag_drop_builder'      => true,
                    'api_access'             => true,
                    'audit_log'              => true,
                    'time_tracking'          => true,
                    'expense_tracking'       => true,
                    'bulk_actions'           => true,
                    'dunning'                => true,
                    'custom_email_template'  => true,
                    'custom_domain'          => true,
                    'tenant_webhooks'        => true,
                    '2fa'                    => true,
                    'csv_import'             => true,
                ],
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
