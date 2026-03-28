<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementItem extends Model
{
    protected $fillable = [
        'procurement_id',
        'sort_order',
        'item_type',
        'name',
        'description',
        'quantity',
        'supplier_origin',
        'supplier_name',
        'purchase_net_unit',
        'sale_net_unit',
        'purchase_vat_rate',
        'sale_vat_rate',
        'is_optional',
        'status_flag',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'purchase_net_unit' => 'float',
            'sale_net_unit' => 'float',
            'purchase_vat_rate' => 'float',
            'sale_vat_rate' => 'float',
            'is_optional' => 'boolean',
        ];
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    public function getPurchaseNetTotalAttribute(): float
    {
        return $this->quantity * $this->purchase_net_unit;
    }

    public function getSaleNetTotalAttribute(): float
    {
        return $this->quantity * $this->sale_net_unit;
    }

    public function getPurchaseVatTotalAttribute(): float
    {
        return $this->purchase_net_total * ($this->purchase_vat_rate / 100);
    }

    public function getSaleVatTotalAttribute(): float
    {
        return $this->sale_net_total * ($this->sale_vat_rate / 100);
    }

    public function getSaleGrossTotalAttribute(): float
    {
        return $this->sale_net_total + $this->sale_vat_total;
    }

    public function getProfitNetAttribute(): float
    {
        return $this->sale_net_total - $this->purchase_net_total;
    }
}