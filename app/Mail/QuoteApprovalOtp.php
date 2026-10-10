<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Mail\Mailable;

class QuoteApprovalOtp extends Mailable
{
    public function __construct(
        public Quote $quote,
        public string $otp,
        public string $purpose = 'accept',
    ) {}

    public function build(): self
    {
        return $this->subject(__('Your approval code for quotation :no', ['no' => $this->quote->quote_no]))
            ->view('emails.quote-otp', [
                'quote' => $this->quote,
                'otp' => $this->otp,
                'purpose' => $this->purpose,
            ]);
    }
}
