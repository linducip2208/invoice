<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillingWebhookController extends Controller
{
    public function handle(Request $request, string $gateway): JsonResponse
    {
        Log::info('Billing webhook', ['gateway' => $gateway, 'body' => $request->all()]);

        $externalSubId = $this->extractSubscriptionId($gateway, $request);

        if (! $externalSubId) {
            return response()->json(['status' => 'no_sub_id'], 400);
        }

        $subscription = Subscription::where('external_subscription_id', $externalSubId)->first();

        if (! $subscription) {
            return response()->json(['status' => 'subscription_not_found'], 404);
        }

        $event = $this->extractEvent($gateway, $request);

        match ($event) {
            'invoice.paid', 'subscription.renewed' => $this->markPaid($subscription, $request),
            'invoice.payment_failed'               => $this->markPastDue($subscription, $request),
            'subscription.cancelled'               => $this->markCancelled($subscription),
            default                                => null,
        };

        return response()->json(['status' => 'ok']);
    }

    protected function markPaid(Subscription $sub, Request $request): void
    {
        $period = $sub->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();

        $sub->update([
            'status'                   => 'active',
            'last_payment_at'          => now(),
            'current_period_starts_at' => now(),
            'current_period_ends_at'   => $period,
        ]);

        SubscriptionInvoice::create([
            'subscription_id'     => $sub->id,
            'workspace_id'        => $sub->workspace_id,
            'external_invoice_id' => $request->input('invoice_id') ?? $request->input('id'),
            'reference_no'        => 'SUB-' . $sub->id . '-' . now()->format('YmdHis'),
            'amount'              => (float) ($request->input('amount') ?? $request->input('amount_paid') ?? 0),
            'currency'            => $sub->currency,
            'status'              => 'paid',
            'paid_at'             => now(),
            'period_starts_at'    => now(),
            'period_ends_at'      => $period,
            'raw_payload'         => $request->all(),
        ]);
    }

    protected function markPastDue(Subscription $sub, Request $request): void
    {
        $sub->update(['status' => 'past_due']);

        SubscriptionInvoice::create([
            'subscription_id'     => $sub->id,
            'workspace_id'        => $sub->workspace_id,
            'external_invoice_id' => $request->input('invoice_id') ?? $request->input('id'),
            'reference_no'        => 'SUB-' . $sub->id . '-FAIL-' . now()->format('YmdHis'),
            'amount'              => (float) ($request->input('amount') ?? 0),
            'currency'            => $sub->currency,
            'status'              => 'failed',
            'raw_payload'         => $request->all(),
        ]);
    }

    protected function markCancelled(Subscription $sub): void
    {
        $sub->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    protected function extractSubscriptionId(string $gateway, Request $request): ?string
    {
        return $request->input('subscription_id')
            ?? $request->input('subscription')
            ?? $request->input('data.object.subscription')
            ?? $request->input('resource.id');
    }

    protected function extractEvent(string $gateway, Request $request): string
    {
        $type = $request->input('type') ?? $request->input('event_type') ?? $request->input('event');

        // Normalize across formats
        return match (true) {
            str_contains((string) $type, 'paid'), str_contains((string) $type, 'PAYMENT.SALE.COMPLETED') => 'invoice.paid',
            str_contains((string) $type, 'failed') => 'invoice.payment_failed',
            str_contains((string) $type, 'cancel'), str_contains((string) $type, 'deleted') => 'subscription.cancelled',
            default => $type ?: 'unknown',
        };
    }
}
