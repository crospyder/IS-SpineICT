<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCatalog extends Model
{
    protected $table = 'service_catalog';

    protected $fillable = [
        'name',
        'code',
        'service_type',
        'description',
        'billing_type',
        'default_unit_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_unit_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function partnerServices(): HasMany
    {
        return $this->hasMany(PartnerService::class);
    }

    public function projectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }
}