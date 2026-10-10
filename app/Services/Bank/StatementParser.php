<?php

namespace App\Services\Bank;

use Illuminate\Support\Carbon;

/**
 * Generic CSV bank statement parser. Heuristic for BCA / Mandiri / BNI / BRI
 * standard CSV exports — auto-detect column positions for date / amount / description.
 *
 * Returns array of: ['trx_date' => Date, 'amount' => float, 'description' => string]
 */
class StatementParser
{
    public function parseCsv(string $absolutePath): array
    {
        $rows = [];
        $handle = fopen($absolutePath, 'r');
        if (! $handle) return [];

        $header = null;
        $colDate = $colAmount = $colDesc = null;
        $colCredit = $colDebit = null;

        while (($cells = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            // Skip empty rows
            if (count($cells) < 2 || ! array_filter($cells)) continue;

            // Detect header row by looking for Date / Tanggal keyword
            if ($header === null) {
                $lower = array_map('strtolower', $cells);
                $hasDate = false;
                foreach ($lower as $i => $h) {
                    if (preg_match('/(date|tanggal|tgl)/i', $h)) { $colDate = $i; $hasDate = true; }
                    if (preg_match('/(description|keterangan|uraian|berita|narasi)/i', $h)) $colDesc = $i;
                    if (preg_match('/(amount|nilai|jumlah(?!.*sisa))/i', $h)) $colAmount = $i;
                    if (preg_match('/(credit|kredit|cr)/i', $h)) $colCredit = $i;
                    if (preg_match('/(debit|db)/i', $h)) $colDebit = $i;
                }
                if ($hasDate) {
                    $header = $cells;
                    continue;
                }
                // No header detected, try heuristic
                if (! $header) continue;
            }

            $date = $this->parseDate($cells[$colDate] ?? null);
            if (! $date) continue;

            $amount = 0;
            if ($colCredit !== null && ! empty($cells[$colCredit])) {
                $amount = $this->parseAmount($cells[$colCredit]);
            } elseif ($colAmount !== null && ! empty($cells[$colAmount])) {
                $amount = $this->parseAmount($cells[$colAmount]);
            }

            // Skip debits (we only care about incoming credit for invoice match)
            if ($amount <= 0) continue;

            $description = $cells[$colDesc] ?? '';

            $rows[] = [
                'trx_date'    => $date->toDateString(),
                'amount'      => $amount,
                'description' => trim($description),
            ];
        }

        fclose($handle);
        return $rows;
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) return null;
        $value = trim($value);
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d', 'd M Y', 'd-M-Y'];
        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $value);
                if ($d) return $d;
            } catch (\Throwable $e) {
                continue;
            }
        }
        try { return Carbon::parse($value); } catch (\Throwable $e) { return null; }
    }

    private function parseAmount(string $value): float
    {
        // Indonesian format: 1.000.000,50 (period thousand, comma decimal)
        // Western: 1,000,000.50
        $value = preg_replace('/[^\d.,-]/', '', $value);
        if (preg_match('/^-?\d+,\d{2}$/', $value)) {
            $value = str_replace(',', '.', $value);
        } elseif (preg_match('/\./', $value) && preg_match('/,/', $value)) {
            // Indonesian: remove dots, replace comma with dot
            if (strrpos($value, ',') > strrpos($value, '.')) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif (preg_match('/,/', $value) && ! preg_match('/\./', $value)) {
            // 1,000 → 1000 (treat as thousand sep)
            $value = str_replace(',', '', $value);
        }
        return abs((float) $value);
    }
}
