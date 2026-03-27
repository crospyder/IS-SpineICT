<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementCost extends Model
{
    protected $fillable = [
        'procurement_id',
        'cost_type',
        'description',
        'quantity',
        'unit',
        'currency',
        'net_amount',
        'vat_rate',
        'supplier_origin',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'net_amount' => 'float',
            'vat_rate' => 'float',
        ];
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    public function getTotalNetAttribute(): float
    {
        return $this->quantity * $this->net_amount;
    }
}