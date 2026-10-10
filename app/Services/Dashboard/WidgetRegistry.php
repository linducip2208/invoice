<?php

namespace App\Services\Dashboard;

class WidgetRegistry
{
    public const WIDGETS = [
        'revenue_trend'   => ['name' => '📈 Revenue Trend',     'desc' => 'Grafik 12 bulan',                'sizes' => ['md', 'lg']],
        'aging_summary'   => ['name' => '⏳ Aging Buckets',      'desc' => 'Bucket bars belum lunas',         'sizes' => ['sm', 'md']],
        'top_clients'     => ['name' => '🏆 Top 5 Klien',        'desc' => 'Berdasarkan revenue',             'sizes' => ['sm', 'md']],
        'top_unpaid'      => ['name' => '⚠️ Top Unpaid Invoice', 'desc' => 'Invoice belum lunas terbesar',    'sizes' => ['md']],
        'recent_invoices' => ['name' => '📄 Invoice Terbaru',   'desc' => 'List 10 invoice terbaru',          'sizes' => ['md', 'lg']],
        'recent_activity' => ['name' => '📰 Activity Feed',      'desc' => '10 aktivitas terakhir',           'sizes' => ['md']],
        'cashflow_30d'    => ['name' => '💰 Cashflow 30d',       'desc' => 'Mini chart',                      'sizes' => ['sm']],
        'pending_approvals' => ['name' => '🛡️ Pending Approvals','desc' => 'Inbox approval',                  'sizes' => ['sm']],
        'time_today'      => ['name' => '⏱️ Hours Today',        'desc' => 'Total time logged hari ini',      'sizes' => ['sm']],
        'tax_due_month'   => ['name' => '🏛️ Tax Due This Month', 'desc' => 'PPN+PPh23 yang harus disetor',    'sizes' => ['sm']],
        'pipeline'        => ['name' => '🔄 Quote Pipeline',     'desc' => 'Quote status funnel',             'sizes' => ['md']],
        'nps_score'       => ['name' => '⭐ NPS Score',          'desc' => 'Average rating klien',            'sizes' => ['sm']],
    ];

    public static function defaultLayout(): array
    {
        return [
            ['widget_type' => 'revenue_trend',   'size' => 'lg', 'position' => 0],
            ['widget_type' => 'aging_summary',   'size' => 'md', 'position' => 1],
            ['widget_type' => 'top_clients',     'size' => 'md', 'position' => 2],
            ['widget_type' => 'recent_invoices', 'size' => 'lg', 'position' => 3],
            ['widget_type' => 'recent_activity', 'size' => 'md', 'position' => 4],
        ];
    }
}
