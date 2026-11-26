<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionDunningMail;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DunSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:dun {--dry-run : Show who would be emailed without sending}';
    protected $description = 'Email workspace owners/admins of past_due subscriptions';

    public function handle(): int
    {
        $subs = Subscription::query()
            ->withoutGlobalScope('workspace')
            ->where('status', 'past_due')
            ->with(['plan', 'workspace.users'])
            ->get();

        if ($subs->isEmpty()) {
            $this->info('No past_due subscriptions.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($subs as $sub) {
            $recipients = $sub->workspace?->users
                ?->filter(fn ($u) => in_array($u->pivot->role ?? null, ['owner', 'admin'], true) && $u->email)
                ->unique('email')
                ->values() ?? collect();

            if ($recipients->isEmpty()) {
                $this->warn("Sub #{$sub->id}: no owner/admin email, skipped.");
                continue;
            }

            foreach ($recipients as $user) {
                if ($this->option('dry-run')) {
                    $this->line("Would email {$user->email} (sub #{$sub->id}).");
                    continue;
                }
                Mail::to($user->email)->queue(new SubscriptionDunningMail($sub, $user->email, $user->name));
            }
            $sent++;
        }

        $this->info("Dunning queued for {$sent} subscription(s).");
        return self::SUCCESS;
    }
}
