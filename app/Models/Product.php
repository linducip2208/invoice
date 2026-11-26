<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'name', 'description', 'unit_price',
        'tax_rate', 'category', 'is_active',
        'track_stock', 'stock_qty', 'low_stock_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'is_active' => 'boolean',
            'track_stock' => 'boolean',
            'stock_qty' => 'decimal:2',
            'low_stock_at' => 'decimal:2',
        ];
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->track_stock && $this->low_stock_at !== null
            && (float) $this->stock_qty <= (float) $this->low_stock_at;
    }
}
