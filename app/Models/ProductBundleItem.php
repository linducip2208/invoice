<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBundleItem extends Model
{
    protected $fillable = ['bundle_id', 'product_id', 'quantity'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2'];
    }

    public function bundle()
    {
        return $this->belongsTo(ProductBundle::class, 'bundle_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
