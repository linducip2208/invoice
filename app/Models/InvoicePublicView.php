<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePublicView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'invoice_id', 'ip_address', 'user_agent', 'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
