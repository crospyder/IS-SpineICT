<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tpm_present' => 'boolean',
            'bitlocker_enabled' => 'boolean',
            'windows_activated' => 'boolean',
            'vpn_detected' => 'boolean',
            'is_domain_joined' => 'boolean',
            'is_azure_ad_joined' => 'boolean',
            'windows_update_service_running' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(InventoryScan::class)->latest();
    }

    public function software(): HasMany
    {
        return $this->hasMany(InventorySoftware::class)->orderBy('name');
    }
}