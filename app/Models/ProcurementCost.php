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
        'net_amount',
        'vat_rate',
        'supplier_origin',
        'include_in_offer',
        'include_in_margin',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'net_amount' => 'float',
            'vat_rate' => 'float',
            'include_in_offer' => 'boolean',
            'include_in_margin' => 'boolean',
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

    public function getVatTotalAttribute(): float
    {
        return $this->total_net * ($this->vat_rate / 100);
    }

    public function getTotalGrossAttribute(): float
    {
        return $this->total_net + $this->vat_total;
    }
}