<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySoftware extends Model
{
    protected $guarded = [];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function inventoryScan(): BelongsTo
    {
        return $this->belongsTo(InventoryScan::class);
    }
}