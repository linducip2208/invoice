<?php

namespace App\Services\LateFee;

use App\Models\Invoice;
use App\Models\InvoiceLateFee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LateFeeApplier
{
    public function shouldApply(Invoice $invoice): bool
    {
        if (in_array($invoice->status, ['paid', 'cancelled', 'draft'])) return false;
        if ($invoice->balance_due <= 0) return false;
        if ($invoice->late_fee_policy === 'none') return false;
        if (! $invoice->due_date->isPast()) return false;

        // For one_time, only apply once
        if ($invoice->late_fee_policy === 'one_time' && $invoice->lateFees()->exists()) return false;

        // For monthly, only apply if last late fee was applied >25 days ago
        if ($invoice->late_fee_policy === 'monthly') {
            $lastFee = $invoice->lateFees()->latest('applied_at')->first();
            if ($lastFee && $lastFee->applied_at->diffInDays(now()) < 25) return false;
        }

        return true;
    }

    public function apply(Invoice $invoice): ?InvoiceLateFee
    {
        return DB::transaction(function () use ($invoice) {
            // Re-check under a row lock: concurrent workers/schedulers must
            // not both pass shouldApply() and compound the fee (TOCTOU).
            $locked = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();
            if (! $this->shouldApply($locked)) {
                return null;
            }

            $type = $locked->late_fee_type ?? 'percent';
            $value = (float) ($locked->late_fee_value ?? 0);

            if ($value <= 0) {
                return null;
            }

            $amount = $type === 'percent'
                ? round((float) $locked->balance_due * $value / 100, 2)
                : round($value, 2);

            if ($amount <= 0) {
                return null;
            }

            $description = $type === 'percent'
                ? "Late fee {$value}% (per {$locked->due_date->format('d M Y')})"
                : "Late fee {$locked->currency} " . number_format($value, 0);

            $fee = InvoiceLateFee::create([
                'workspace_id' => $locked->workspace_id,
                'invoice_id' => $locked->id,
                'applied_at' => now(),
                'amount' => $amount,
                'type' => $type,
                'percent_value' => $type === 'percent' ? $value : null,
                'fixed_amount' => $type === 'fixed' ? $value : null,
                'description' => $description,
            ]);

            // Update invoice totals
            $locked->late_fee_total = (float) $locked->late_fee_total + $amount;
            $locked->total = (float) $locked->total + $amount;
            $locked->balance_due = (float) $locked->balance_due + $amount;
            $locked->save();

            return $fee;
        });
    }

    public function reverse(InvoiceLateFee $fee): void
    {
        $invoice = $fee->invoice;
        if (! $invoice) {
            $fee->delete();
            return;
        }
        $invoice->late_fee_total = max(0, (float) $invoice->late_fee_total - (float) $fee->amount);
        $invoice->total = max(0, (float) $invoice->total - (float) $fee->amount);
        $invoice->balance_due = max(0, (float) $invoice->balance_due - (float) $fee->amount);
        $invoice->save();
        $fee->delete();
    }
}
