<?php

namespace App\Services\Analytics;

use App\Models\Invoice;

class LateRiskScorer
{
    /**
     * Composite weighted score 0-100. Higher = more risky.
     */
    public function score(Invoice $invoice): array
    {
        $factors = [];
        $score = 0;

        $client = $invoice->client;

        // Factor 1: client reliability (40% weight)
        if ($client?->payment_reliability) {
            $reliabilityScore = match ($client->payment_reliability) {
                'excellent' => 5,
                'good'      => 20,
                'fair'      => 50,
                'poor'      => 80,
            };
            $score += $reliabilityScore * 0.4;
            $factors[] = "Reliability klien: {$client->payment_reliability}";
        } else {
            $score += 30 * 0.4;
            $factors[] = 'Klien baru — data belum cukup';
        }

        // Factor 2: invoice amount vs client avg (20%)
        if ($client && $client->total_paid_count > 0) {
            $avgInvoiceAmount = (float) Invoice::withoutGlobalScope('workspace')
                ->where('client_id', $client->id)
                ->where('status', 'paid')
                ->avg('total') ?? 0;

            if ($avgInvoiceAmount > 0 && (float) $invoice->total > $avgInvoiceAmount * 2) {
                $score += 30 * 0.2;
                $factors[] = 'Jumlah jauh lebih besar dari biasanya (>2× rata-rata)';
            } elseif ($avgInvoiceAmount > 0 && (float) $invoice->total > $avgInvoiceAmount * 1.5) {
                $score += 15 * 0.2;
                $factors[] = 'Jumlah lebih besar dari biasanya';
            }
        }

        // Factor 3: due date too short (15%)
        $dueDays = $invoice->issue_date->diffInDays($invoice->due_date);
        if ($dueDays < 7) {
            $score += 30 * 0.15;
            $factors[] = "Due date sangat ketat ({$dueDays} hari)";
        } elseif ($dueDays < 14) {
            $score += 15 * 0.15;
            $factors[] = "Due date pendek ({$dueDays} hari)";
        }

        // Factor 4: workspace recent late rate (15%)
        $totalRecent = Invoice::withoutGlobalScope('workspace')
            ->where('workspace_id', $invoice->workspace_id)
            ->where('issue_date', '>=', now()->subMonths(3))
            ->count();
        $lateRecent = Invoice::withoutGlobalScope('workspace')
            ->where('workspace_id', $invoice->workspace_id)
            ->where('issue_date', '>=', now()->subMonths(3))
            ->whereIn('status', ['overdue', 'partial'])
            ->where('due_date', '<', now())
            ->count();
        if ($totalRecent > 0) {
            $lateRate = $lateRecent / $totalRecent;
            if ($lateRate > 0.3) {
                $score += 30 * 0.15;
                $factors[] = sprintf('Workspace rate telat tinggi (%.0f%% di 3 bulan terakhir)', $lateRate * 100);
            }
        }

        // Factor 5: end-of-quarter / month (10%)
        $month = (int) $invoice->due_date->format('n');
        if (in_array($month, [3, 6, 9, 12]) && (int) $invoice->due_date->format('j') >= 25) {
            $score += 10 * 0.10;
            $factors[] = 'Jatuh tempo akhir kuartal (klien sering delay)';
        }

        $finalScore = (int) min(100, max(0, round($score)));

        return [
            'score'   => $finalScore,
            'factors' => $factors,
            'level'   => match (true) {
                $finalScore <= 30 => 'low',
                $finalScore <= 60 => 'medium',
                default           => 'high',
            },
        ];
    }

    /**
     * Compute and persist score on the invoice.
     */
    public function scoreAndSave(Invoice $invoice): void
    {
        $result = $this->score($invoice);
        $invoice->update([
            'late_risk_score'   => $result['score'],
            'late_risk_factors' => $result['factors'],
        ]);
    }
}
