<?php

namespace App\Services\Billing;

use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Format-agnostic recurring-billing adapter.
 *
 * Per global rule: NO hardcoded vendor names. Adapter dispatches by
 * `recurring_format` set on the gateway record (stripe-rest, paypal-rest,
 * xendit-recurring, midtrans-recurring, generic-rest). Owner of the app
 * configures the gateway in /admin gateway page.
 */
class RecurringBillingAdapter
{
    /**
     * Create a recurring subscription on the configured gateway.
     * Returns ['checkout_url' => string, 'external_subscription_id' => string]
     * or throws BillingException.
     */
    public function createSubscription(
        PaymentGateway $gateway,
        Plan $plan,
        Subscription $subscription,
        array $context = []
    ): array {
        if (! $gateway->supports_recurring) {
            throw new \RuntimeException("Gateway {$gateway->name} tidak support recurring.");
        }

        $payload = [
            'plan_slug'        => $plan->slug,
            'plan_name'        => $plan->name,
            'amount'           => $plan->priceFor($subscription->currency, $subscription->billing_period),
            'currency'         => $subscription->currency,
            'interval'         => $subscription->billing_period === 'yearly' ? 'year' : 'month',
            'workspace_id'     => $subscription->workspace_id,
            'customer_email'   => $context['email'] ?? null,
            'customer_name'    => $context['name'] ?? null,
            'success_url'      => $context['success_url'] ?? route('billing.show'),
            'cancel_url'       => $context['cancel_url'] ?? route('billing.show'),
            'webhook_url'      => route('webhooks.billing', ['gateway' => $gateway->gateway ?? 'generic']),
            'metadata'         => ['subscription_id' => $subscription->id],
        ];

        return match ($gateway->recurring_format) {
            'stripe-rest'        => $this->stripeRest($gateway, $payload),
            'paypal-rest'        => $this->paypalRest($gateway, $payload),
            'xendit-recurring'   => $this->xenditRecurring($gateway, $payload),
            'midtrans-recurring' => $this->midtransRecurring($gateway, $payload),
            default              => $this->genericRest($gateway, $payload),
        };
    }

    /** Stripe Checkout Session for subscription. */
    protected function stripeRest(PaymentGateway $gw, array $p): array
    {
        $base = rtrim($gw->base_url ?? 'https://api.stripe.com/v1', '/');

        $res = Http::withBasicAuth($gw->api_key_decrypted ?? '', '')
            ->asForm()
            ->post($base . '/checkout/sessions', [
                'mode'                              => 'subscription',
                'success_url'                       => $p['success_url'] . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'                        => $p['cancel_url'],
                'customer_email'                    => $p['customer_email'],
                'line_items[0][price_data][currency]'    => strtolower($p['currency']),
                'line_items[0][price_data][unit_amount]' => (int) ($p['amount'] * 100),
                'line_items[0][price_data][recurring][interval]' => $p['interval'],
                'line_items[0][price_data][product_data][name]'  => $p['plan_name'],
                'line_items[0][quantity]'           => 1,
                'metadata[subscription_id]'         => $p['metadata']['subscription_id'],
                'metadata[workspace_id]'            => $p['workspace_id'],
            ]);

        $this->throwIfFail($res, 'stripe-rest');
        $j = $res->json();

        return [
            'checkout_url'             => $j['url'] ?? null,
            'external_subscription_id' => $j['id'] ?? null,
            'raw'                      => $j,
        ];
    }

    /** PayPal Subscriptions API. */
    protected function paypalRest(PaymentGateway $gw, array $p): array
    {
        $base = rtrim($gw->base_url ?? 'https://api-m.paypal.com', '/');

        // Step 1: get OAuth token
        $token = Http::withBasicAuth($gw->api_key_decrypted ?? '', $gw->api_secret_decrypted ?? '')
            ->asForm()
            ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials'])
            ->json('access_token');

        if (! $token) {
            throw new \RuntimeException('PayPal OAuth failed.');
        }

        // Step 2: create subscription (assumes plan_id pre-created in PayPal)
        $res = Http::withToken($token)->post($base . '/v1/billing/subscriptions', [
            'plan_id'    => $gw->extra_headers['paypal_plan_id_' . $p['plan_slug']] ?? '',
            'application_context' => [
                'return_url' => $p['success_url'],
                'cancel_url' => $p['cancel_url'],
            ],
        ]);

        $this->throwIfFail($res, 'paypal-rest');
        $j = $res->json();
        $approve = collect($j['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        return [
            'checkout_url'             => $approve,
            'external_subscription_id' => $j['id'] ?? null,
            'raw'                      => $j,
        ];
    }

    /** Xendit Recurring API. */
    protected function xenditRecurring(PaymentGateway $gw, array $p): array
    {
        $base = rtrim($gw->base_url ?? 'https://api.xendit.co', '/');

        $res = Http::withBasicAuth($gw->api_key_decrypted ?? '', '')
            ->post($base . '/recurring/plans', [
                'reference_id'  => 'sub-' . $p['metadata']['subscription_id'],
                'customer_id'   => $p['customer_email'],
                'recurring_action' => 'PAYMENT',
                'currency'      => $p['currency'],
                'amount'        => $p['amount'],
                'schedule'      => [
                    'reference_id' => 'sched-' . $p['metadata']['subscription_id'],
                    'interval'     => 'MONTH',
                    'interval_count' => $p['interval'] === 'year' ? 12 : 1,
                ],
                'success_return_url' => $p['success_url'],
                'failure_return_url' => $p['cancel_url'],
            ]);

        $this->throwIfFail($res, 'xendit-recurring');
        $j = $res->json();

        return [
            'checkout_url'             => $j['actions'][0]['url'] ?? $p['success_url'],
            'external_subscription_id' => $j['id'] ?? null,
            'raw'                      => $j,
        ];
    }

    /** Midtrans recurring (Snap + token-based). */
    protected function midtransRecurring(PaymentGateway $gw, array $p): array
    {
        $base = rtrim($gw->base_url ?? 'https://app.midtrans.com/snap/v1', '/');

        $res = Http::withBasicAuth($gw->api_key_decrypted ?? '', '')
            ->post($base . '/transactions', [
                'transaction_details' => [
                    'order_id'     => 'sub-' . $p['metadata']['subscription_id'] . '-' . time(),
                    'gross_amount' => $p['amount'],
                ],
                'credit_card' => ['secure' => true, 'save_card' => true],
                'customer_details' => [
                    'email' => $p['customer_email'],
                    'first_name' => $p['customer_name'],
                ],
                'callbacks' => ['finish' => $p['success_url']],
            ]);

        $this->throwIfFail($res, 'midtrans-recurring');
        $j = $res->json();

        return [
            'checkout_url'             => $j['redirect_url'] ?? null,
            'external_subscription_id' => $j['token'] ?? null,
            'raw'                      => $j,
        ];
    }

    /** Generic REST — gateway harus accept POST {amount, currency, interval, ...}. */
    protected function genericRest(PaymentGateway $gw, array $p): array
    {
        $base = rtrim($gw->base_url ?? '', '/');
        if (! $base) {
            throw new \RuntimeException("Gateway {$gw->name} tidak punya base_url.");
        }

        $headers = collect($gw->extra_headers ?? [])->toArray();
        if (! empty($gw->api_key_decrypted ?? null)) {
            $headers['Authorization'] = 'Bearer ' . $gw->api_key_decrypted;
        }

        $res = Http::withHeaders($headers)->post($base . '/subscriptions', $p);
        $this->throwIfFail($res, 'generic-rest');
        $j = $res->json();

        return [
            'checkout_url'             => $j['checkout_url'] ?? $j['url'] ?? null,
            'external_subscription_id' => $j['id'] ?? $j['subscription_id'] ?? null,
            'raw'                      => $j,
        ];
    }

    protected function throwIfFail($res, string $format): void
    {
        if (! $res->successful()) {
            Log::error("Recurring billing fail [{$format}]", ['status' => $res->status(), 'body' => $res->body()]);
            throw new \RuntimeException("Billing API gagal ({$format}): " . $res->status() . ' — ' . substr($res->body(), 0, 300));
        }
    }
}
