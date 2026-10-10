<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class EscalateOverdueOrders extends Command
{
    protected $signature = 'invoices:escalate-overdue';
    protected $description = 'Escalate overdue invoices: sent → overdue, mark chronic overdue for review';

    public function handle(): int
    {
        // Mark "sent" invoices past due date as "overdue"
        $now = now();
        $overdue = Invoice::where('status', 'sent')
            ->where('due_date', '<', $now)
            ->update(['status' => 'overdue']);

        $this->info("Marked {$overdue} invoices as overdue.");

        // Log severely overdue (90+ days) for admin review
        $chronic = Invoice::where('status', 'overdue')
            ->where('due_date', '<', $now->subDays(90))
            ->count();

        if ($chronic > 0) {
            $this->warn("{$chronic} invoices are 90+ days overdue — may need manual follow-up.");
        }

        return 0;
    }
}
