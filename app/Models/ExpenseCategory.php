<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'name', 'icon', 'color', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }
}
