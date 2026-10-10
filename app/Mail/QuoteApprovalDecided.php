<?php

namespace App\Mail;

use App\Models\Quote;
use App\Models\QuoteApprovalEvent;
use Illuminate\Mail\Mailable;

class QuoteApprovalDecided extends Mailable
{
    public function __construct(
        public Quote $quote,
        public QuoteApprovalEvent $event,
    ) {}

    public function build(): self
    {
        $verb = $this->event->outcome === 'accepted' ? __('accepted') : __('rejected');
        return $this->subject(__('Quotation :no :verb (rev :rev)', [
                'no' => $this->quote->quote_no,
                'verb' => $verb,
                'rev' => $this->event->revision,
            ]))
            ->view('emails.quote-decided', [
                'quote' => $this->quote,
                'event' => $this->event,
            ]);
    }
}
