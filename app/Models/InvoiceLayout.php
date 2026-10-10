<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLayout extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'blocks',
        'settings',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'blocks'     => 'array',
            'settings'   => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public static function defaultBlocks(): array
    {
        return [
            ['type' => 'header',     'props' => ['show_logo' => true, 'align' => 'left']],
            ['type' => 'bill_to',    'props' => ['title' => 'Bill To']],
            ['type' => 'dates',      'props' => ['show_due' => true]],
            ['type' => 'items',      'props' => ['show_tax' => true, 'show_qty' => true]],
            ['type' => 'totals',     'props' => []],
            ['type' => 'notes',      'props' => ['title' => 'Notes']],
            ['type' => 'terms',      'props' => ['title' => 'Terms & Conditions']],
            ['type' => 'footer',     'props' => ['text' => 'Thank you for your business!']],
        ];
    }
}
