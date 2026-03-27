<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Procurement extends Model
{
    protected $fillable = [
        'partner_id',
        'title',
        'reference_no',
        'status',
        'offer_date',
        'valid_until',
        'default_sale_currency',
        'default_purchase_currency',
        'fx_eur_to_usd',
        'fx_usd_to_eur',
        'vat_rate',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'offer_date' => 'date',
            'valid_until' => 'date',
            'fx_eur_to_usd' => 'float',
            'fx_usd_to_eur' => 'float',
            'vat_rate' => 'float',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function partner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Partner::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(ProcurementCost::class);
    }
}