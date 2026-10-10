<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceReminder;
use App\Services\Reminder\ReminderScheduler;
use Illuminate\Http\Request;

class InvoiceReminderController extends Controller
{
    public function __construct(private ReminderScheduler $scheduler) {}

    public function index(Invoice $invoice)
    {
        $reminders = $invoice->reminders()->orderBy('scheduled_at')->get();

        return view('invoices.reminders', compact('invoice', 'reminders'));
    }

    public function schedule(Invoice $invoice)
    {
        $count = $this->scheduler->scheduleFor($invoice);

        return back()->with('success', "{$count} reminder dijadwalkan.");
    }

    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'scheduled_at'     => 'required|date|after:now',
            'template_subject' => 'required|string|max:200',
            'template_body'    => 'required|string|max:5000',
        ]);

        InvoiceReminder::create([
            'workspace_id'     => $invoice->workspace_id,
            'invoice_id'       => $invoice->id,
            'schedule_offset'  => 0,
            'scheduled_at'     => $validated['scheduled_at'],
            'template_subject' => $validated['template_subject'],
            'template_body'    => $validated['template_body'],
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Reminder ditambahkan.');
    }

    public function cancel(InvoiceReminder $reminder)
    {
        $reminder->update(['status' => 'cancelled']);

        return back()->with('success', 'Reminder dibatalkan.');
    }
}
