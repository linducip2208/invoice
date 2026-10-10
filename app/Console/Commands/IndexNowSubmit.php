<?php

namespace App\Console\Commands;

use App\Services\Seo\IndexNowService;
use Illuminate\Console\Command;

class IndexNowSubmit extends Command
{
    protected $signature = 'seo:indexnow
                            {--url= : Submit a single URL (relative path or full URL)}
                            {--pseo : Submit all PSEO pages}';

    protected $description = 'Submit URLs to IndexNow (Bing, Yandex, Seznam, Naver)';

    public function handle(IndexNowService $service): int
    {
        if ($this->option('url')) {
            $result = $service->submitSingle($this->option('url'));
            $this->info("Submitted 1 URL. Success: " . ($result['success'] ? 'yes' : 'no'));
            return 0;
        }

        if ($this->option('pseo')) {
            $this->info('Submitting PSEO pages to IndexNow...');
            $result = $service->submitPseoPages();
            $this->info("New URLs submitted: {$result['submitted']}");
            return 0;
        }

        // Default: submit PSEO pages
        $this->info('Submitting PSEO pages to IndexNow...');
        $result = $service->submitPseoPages();

        if ($result['success']) {
            $this->info("Successfully submitted {$result['submitted']} URLs.");
        } else {
            $this->error('Submission failed: ' . ($result['message'] ?? 'unknown error'));
        }

        return 0;
    }
}
