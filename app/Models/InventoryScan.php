<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryScan extends Model
{
    protected $guarded = [];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function software(): HasMany
    {
        return $this->hasMany(InventorySoftware::class)->orderBy('name');
    }
}