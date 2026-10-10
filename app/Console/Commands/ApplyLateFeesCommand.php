<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\LateFee\LateFeeApplier;
use Illuminate\Console\Command;

class ApplyLateFeesCommand extends Command
{
    protected $signature = 'late-fees:apply {--dry-run}';
    protected $description = 'Apply late fees to overdue invoices based on policy';

    public function handle(LateFeeApplier $applier): int
    {
        $candidates = Invoice::query()
            ->withoutGlobalScope('workspace')
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('late_fee_policy', '!=', 'none')
            ->where('balance_due', '>', 0)
            ->where('due_date', '<', now())
            ->limit(500)
            ->get();

        if ($candidates->isEmpty()) {
            $this->info('No invoices eligible for late fees.');
            return self::SUCCESS;
        }

        $applied = $skipped = 0;
        foreach ($candidates as $inv) {
            if (! $applier->shouldApply($inv)) {
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("  [DRY] Would apply late fee to {$inv->invoice_no}");
                continue;
            }

            $fee = $applier->apply($inv);
            if ($fee) {
                $applied++;
                $this->line("  ✓ {$inv->invoice_no} +{$inv->currency} " . number_format($fee->amount, 0));
            } else {
                $skipped++;
            }
        }

        $this->info("Done. Applied: {$applied}, Skipped: {$skipped}");
        return self::SUCCESS;
    }
}
