<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\Reminder\ReminderScheduler;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public string $subjectTemplate,
        public string $bodyTemplate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ReminderScheduler::render($this->subjectTemplate, $this->invoice),
            to: [$this->invoice->client->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-reminder',
            with: [
                'bodyText' => ReminderScheduler::render($this->bodyTemplate, $this->invoice),
                'invoice'  => $this->invoice,
            ],
        );
    }
}
