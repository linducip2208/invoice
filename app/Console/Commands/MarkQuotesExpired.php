<?php

namespace App\Console\Commands;

use App\Services\QuoteApprovalService;
use Illuminate\Console\Command;

class MarkQuotesExpired extends Command
{
    protected $signature = 'quotes:mark-expired';
    protected $description = 'Mark past-expiry sent quotes as expired';

    public function handle(QuoteApprovalService $approvals): int
    {
        $count = $approvals->markExpiredDue();
        $this->info("Marked {$count} quotes expired.");
        return 0;
    }
}
