<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
    protected $fillable = [
        'project_id',
        'service_catalog_id',
        'item_type',
        'name',
        'description',
        'quantity',
        'purchase_has_vat',
        'cost_price_net',
        'sell_price_net',
        'profit_net',
        'sale_status',
        'is_manual_cost',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'purchase_has_vat' => 'boolean',
            'cost_price_net' => 'decimal:2',
            'sell_price_net' => 'decimal:2',
            'profit_net' => 'decimal:2',
            'is_manual_cost' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function serviceCatalog(): BelongsTo
    {
        return $this->belongsTo(ServiceCatalog::class);
    }
}