<?php

namespace App\Services\Bank;

use App\Models\BankReconcileLine;
use App\Models\Invoice;

class Matcher
{
    /**
     * Top-N saran invoice untuk satu baris, beserta alasannya.
     * @return array<int, array{invoice: Invoice, confidence: string, reason: string}>
     */
    public function suggest(BankReconcileLine $line, int $limit = 3): array
    {
        $candidates = Invoice::query()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->with('client')
            ->get();

        $scored = [];
        foreach ($candidates as $inv) {
            // Referensi nomor invoice di deskripsi (terkuat).
            if ($inv->invoice_no && stripos($line->description, $inv->invoice_no) !== false) {
                $scored[] = ['invoice' => $inv, 'confidence' => 'high', 'reason' => __('No. invoice cocok di keterangan'), 'score' => 100];
                continue;
            }
            $score = 0;
            $reasons = [];

            if (abs((float) $inv->balance_due - (float) $line->amount) < 0.01) {
                $score += 50;
                $reasons[] = __('nominal pas');
                $diff = abs($line->trx_date->diffInDays($inv->issue_date));
                if ($diff <= 7) {
                    $score += 30;
                    $reasons[] = __('tanggal dekat');
                } elseif ($diff <= 30) {
                    $score += 10;
                    $reasons[] = __('tanggal ≤30 hari');
                }
            } elseif (abs((float) $inv->total - (float) $line->amount) < 0.01) {
                $score += 20;
                $reasons[] = __('nominal = total invoice');
            }

            // Nama klien / perusahaan muncul di deskripsi.
            foreach ([$inv->client?->name, $inv->client?->company_name] as $needle) {
                if ($needle && mb_strlen($needle) >= 4 && stripos($line->description, $needle) !== false) {
                    $score += 25;
                    $reasons[] = __('nama klien cocok') . " ({$needle})";
                    break;
                }
            }

            if ($score > 0) {
                $scored[] = [
                    'invoice' => $inv,
                    'confidence' => $score >= 70 ? 'high' : ($score >= 35 ? 'medium' : 'low'),
                    'reason' => implode(', ', $reasons),
                    'score' => $score,
                ];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $limit);
    }
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
