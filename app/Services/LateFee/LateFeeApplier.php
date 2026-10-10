<?php

namespace App\Services\LateFee;

use App\Models\Invoice;
use App\Models\InvoiceLateFee;
use Illuminate\Support\Carbon;

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
        if (! $this->shouldApply($invoice)) return null;

        $type = $invoice->late_fee_type ?? 'percent';
        $value = (float) ($invoice->late_fee_value ?? 0);

        if ($value <= 0) return null;

        $amount = $type === 'percent'
            ? round((float) $invoice->balance_due * $value / 100, 2)
            : round($value, 2);

        if ($amount <= 0) return null;

        $description = $type === 'percent'
            ? "Late fee {$value}% (per {$invoice->due_date->format('d M Y')})"
            : "Late fee {$invoice->currency} " . number_format($value, 0);

        $fee = InvoiceLateFee::create([
            'workspace_id'  => $invoice->workspace_id,
            'invoice_id'    => $invoice->id,
            'applied_at'    => now(),
            'amount'        => $amount,
            'type'          => $type,
            'percent_value' => $type === 'percent' ? $value : null,
            'fixed_amount'  => $type === 'fixed' ? $value : null,
            'description'   => $description,
        ]);

        // Update invoice totals
        $invoice->late_fee_total = (float) $invoice->late_fee_total + $amount;
        $invoice->total = (float) $invoice->total + $amount;
        $invoice->balance_due = (float) $invoice->balance_due + $amount;
        $invoice->save();

        return $fee;
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
