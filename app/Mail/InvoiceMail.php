<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Mail\Mailable;

class InvoiceMail extends Mailable
{
    public $invoice;
    public $subjectLine;
    public $messageBody;

    public function __construct(
        Invoice $invoice,
        string $subject,
        string $message,
        protected string $pdfPath,
    ) {
        $this->invoice = $invoice;
        $this->subjectLine = $subject;
        $this->messageBody = $message;
    }

    public function build(): self
    {
        $mail = $this->subject($this->subjectLine)
            ->view('emails.invoice', [
                'invoice'     => $this->invoice,
                'workspace'   => $this->invoice->workspace,
                'messageBody' => $this->messageBody,
            ]);

        if ($this->pdfPath && file_exists(storage_path('app/public/' . $this->pdfPath))) {
            $mail->attach(
                storage_path('app/public/' . $this->pdfPath),
                [
                    'as'   => $this->invoice->invoice_no . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
        }

        return $mail;
    }
}
