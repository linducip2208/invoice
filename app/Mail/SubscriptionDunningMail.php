<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionDunningMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $toEmail,
        public string $toName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Pembayaran langganan :plan gagal — aksi diperlukan', ['plan' => $this->subscription->plan?->name ?? '']),
            to: [$this->toEmail],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-dunning',
            with: [
                'subscription' => $this->subscription,
                'name' => $this->toName,
                'billingUrl' => route('billing.show'),
            ],
        );
    }
}
