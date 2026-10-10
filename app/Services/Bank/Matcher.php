<?php

namespace App\Services\Bank;

use App\Models\BankReconcileLine;
use App\Models\Invoice;

class Matcher
{
    /**
     * For each line, find best matching invoice & set match_confidence.
     */
    public function autoMatch(BankReconcileLine $line): void
    {
        $candidates = Invoice::query()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->get();

        $bestMatch = null;
        $bestConfidence = 'none';
        $bestMethod = 'none';

        foreach ($candidates as $inv) {
            // Strategy 1: invoice_no in description (high)
            if ($inv->invoice_no && stripos($line->description, $inv->invoice_no) !== false) {
                $bestMatch = $inv;
                $bestConfidence = 'high';
                $bestMethod = 'auto_reference';
                break;
            }

            // Strategy 2: exact amount + date in last 7 days
            if (abs((float) $inv->balance_due - (float) $line->amount) < 0.01) {
                $issueDiff = $line->trx_date->diffInDays($inv->issue_date);
                if ($issueDiff <= 30) {
                    if (! $bestMatch || $bestConfidence === 'low') {
                        $bestMatch = $inv;
                        $bestConfidence = $issueDiff <= 7 ? 'high' : 'medium';
                        $bestMethod = 'auto_amount_date';
                    }
                }
            }
        }

        if ($bestMatch) {
            $line->update([
                'matched_invoice_id' => $bestMatch->id,
                'match_confidence'   => $bestConfidence,
                'match_method'       => $bestMethod,
            ]);
        }
    }
}
