<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'name', 'description', 'price', 'is_active'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function items()
    {
        return $this->hasMany(ProductBundleItem::class, 'bundle_id')->orderBy('id');
    }

    public function getComponentsTotalAttribute(): float
    {
        return (float) $this->items->sum(fn ($i) => (float) $i->quantity * (float) ($i->product?->unit_price ?? 0));
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->price !== null ? (float) $this->price : $this->components_total;
    }
}
