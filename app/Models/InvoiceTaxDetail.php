<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class InvoiceTaxDetail extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'ppn_rate', 'ppn_amount',
        'pph23_rate', 'pph23_amount', 'pph23_object_code',
        'efaktur_status', 'efaktur_no', 'efaktur_qr_code', 'efaktur_xml_path',
        'coretax_response_json',
    ];

    protected function casts(): array
    {
        return [
            'coretax_response_json' => 'array',
            'ppn_rate'      => 'decimal:2',
            'ppn_amount'    => 'decimal:2',
            'pph23_rate'    => 'decimal:2',
            'pph23_amount'  => 'decimal:2',
        ];
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }
}
