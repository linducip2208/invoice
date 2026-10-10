<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class InvoiceComment extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'author_user_id', 'author_client_id',
        'author_name', 'author_email', 'body', 'is_internal',
        'parent_id', 'attachments_json',
    ];

    protected function casts(): array
    {
        return [
            'is_internal'      => 'boolean',
            'attachments_json' => 'array',
        ];
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function author()  { return $this->belongsTo(User::class, 'author_user_id'); }
    public function client()  { return $this->belongsTo(Client::class, 'author_client_id'); }
    public function parent()  { return $this->belongsTo(self::class, 'parent_id'); }
    public function replies() { return $this->hasMany(self::class, 'parent_id'); }
}
