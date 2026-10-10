<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'subject_type', 'subject_id', 'signer_name',
        'signer_email', 'signer_role', 'signature_image_path',
        'signature_typed', 'ip', 'ua', 'signed_at', 'verification_hash',
    ];

    protected function casts(): array
    {
        return ['signed_at' => 'datetime'];
    }

    public function subject() { return $this->morphTo(); }
}
