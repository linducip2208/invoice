<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class InvoiceAttachment extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['invoice_id', 'workspace_id', 'file_name', 'file_path', 'file_size', 'mime_type'];

    public function invoice() { return $this->belongsTo(Invoice::class); }
}
