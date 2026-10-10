<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'client_id', 'invoice_no', 'status',
        'recurring_invoice_id', 'template', 'invoice_layout_id',
        'primary_color', 'accent_color',
        'issue_date', 'due_date', 'subtotal', 'tax_rate',
        'tax_amount', 'discount_type', 'discount_value', 'discount_amount',
        'total', 'amount_paid', 'balance_due', 'currency',
        'notes', 'terms', 'public_token', 'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'last_sent_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (! $invoice->public_token) {
                $invoice->public_token = (string) Str::uuid();
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recurringInvoice()
    {
        return $this->belongsTo(RecurringInvoice::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function publicViews()
    {
        return $this->hasMany(InvoicePublicView::class);
    }

    public function attachments()
    {
        return $this->hasMany(InvoiceAttachment::class);
    }

    public function layout()
    {
        return $this->belongsTo(InvoiceLayout::class, 'invoice_layout_id');
    }

    public function reminders()    { return $this->hasMany(InvoiceReminder::class); }
    public function lateFees()     { return $this->hasMany(InvoiceLateFee::class); }
    public function creditNotes()  { return $this->hasMany(CreditNote::class); }
    public function comments()     { return $this->hasMany(InvoiceComment::class); }
    public function taxDetail()    { return $this->hasOne(InvoiceTaxDetail::class); }
    public function project()      { return $this->belongsTo(Project::class); }
    public function quote()        { return $this->belongsTo(Quote::class); }
    public function assignee()     { return $this->belongsTo(User::class, 'assigned_to'); }
    public function signatures()   { return $this->morphMany(Signature::class, 'subject'); }

    public function getPublicUrlAttribute(): string
    {
        return route('public.invoice.show', $this->public_token);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'paid' => 'green',
            'partial' => 'yellow',
            'overdue' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }
}
