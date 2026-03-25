<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'partner_id',
        'parent_asset_id',
        'asset_type',
        'name',
        'hostname',
        'domain_name',
        'ip_address',
        'local_ip',
        'public_ip',
        'mac_address',
        'manufacturer',
        'model',
        'serial_number',
        'os_name',
        'os_version',
        'cpu',
        'ram_gb',
        'storage_summary',
        'location',
        'status',
        'purchased_on',
        'installed_on',
        'warranty_until',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchased_on' => 'date',
            'installed_on' => 'date',
            'warranty_until' => 'date',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'parent_asset_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Asset::class, 'parent_asset_id');
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class);
    }

    public function licences(): HasMany
    {
        return $this->hasMany(Licence::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }
}