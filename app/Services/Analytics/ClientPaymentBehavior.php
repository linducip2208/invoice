<?php

namespace App\Services\Analytics;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;

class ClientPaymentBehavior
{
    /**
     * Recompute average payment days & reliability for a client.
     * Called from Payment observer after a payment is created.
     */
    public function recompute(Client $client): void
    {
        $paidInvoices = Invoice::withoutGlobalScope('workspace')
            ->where('client_id', $client->id)
            ->where('status', 'paid')
            ->select('id', 'issue_date', 'due_date')
            ->get();

        $totalInvoices = Invoice::withoutGlobalScope('workspace')
            ->where('client_id', $client->id)
            ->count();

        if ($paidInvoices->count() < 1) {
            $client->update([
                'total_invoices_count' => $totalInvoices,
                'total_paid_count'     => 0,
                'avg_payment_days'     => null,
                'payment_reliability'  => null,
            ]);
            return;
        }

        $daysList = [];
        $onTimeCount = 0;
        foreach ($paidInvoices as $inv) {
            $lastPayment = Payment::withoutGlobalScope('workspace')
                ->where('invoice_id', $inv->id)
                ->latest('payment_date')
                ->first();
            if (! $lastPayment) continue;

            $days = $inv->issue_date->diffInDays($lastPayment->payment_date);
            $daysList[] = $days;
            if ($lastPayment->payment_date->lte($inv->due_date)) $onTimeCount++;
        }

        if (empty($daysList)) return;

        // Trim outlier 5% top
        sort($daysList);
        $trimCount = (int) (count($daysList) * 0.05);
        if ($trimCount > 0) $daysList = array_slice($daysList, 0, count($daysList) - $trimCount);

        $avgDays = (int) round(array_sum($daysList) / count($daysList));
        $onTimeRate = $onTimeCount / count($paidInvoices);

        $reliability = match (true) {
            count($daysList) < 3                  => null,
            $avgDays <= 0 && $onTimeRate >= 0.95  => 'excellent',
            $avgDays <= 5 && $onTimeRate >= 0.80  => 'good',
            $avgDays <= 14 && $onTimeRate >= 0.60 => 'fair',
            default                               => 'poor',
        };

        $client->update([
            'total_invoices_count' => $totalInvoices,
            'total_paid_count'     => $paidInvoices->count(),
            'avg_payment_days'     => $avgDays,
            'payment_reliability'  => $reliability,
        ]);
    }
}
