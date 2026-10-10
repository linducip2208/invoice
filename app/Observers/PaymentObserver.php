<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\Analytics\ClientPaymentBehavior;

class PaymentObserver
{
    public function __construct(private ClientPaymentBehavior $behavior) {}

    public function created(Payment $payment): void
    {
        $this->updateClientBehavior($payment);
    }

    public function deleted(Payment $payment): void
    {
        $this->updateClientBehavior($payment);
    }

    private function updateClientBehavior(Payment $payment): void
    {
        $client = $payment->invoice?->client;
        if ($client) {
            try {
                $this->behavior->recompute($client);
            } catch (\Throwable $e) {
                logger()->warning('ClientPaymentBehavior recompute failed: ' . $e->getMessage());
            }
        }
    }
}
