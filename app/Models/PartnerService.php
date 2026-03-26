<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerService extends Model
{
    protected $fillable = [
        'partner_id',
        'service_catalog_id',
        'partner_contact_id',
        'service_type',
        'name',
        'domain_name',
        'provider',
        'registrar',
        'status',
        'renewal_period',
        'auto_renew',
        'starts_on',
        'expires_on',
        'renewal_date',
        'admin_link',
        'renewal_method',
        'resolved',
        'last_alarm_sent_at',
        'cost_price',
        'sell_price',
        'currency',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'auto_renew' => 'boolean',
            'resolved' => 'boolean',
            'is_active' => 'boolean',
            'starts_on' => 'date',
            'expires_on' => 'date',
            'renewal_date' => 'date',
            'last_alarm_sent_at' => 'datetime',
            'cost_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function serviceCatalog(): BelongsTo
    {
        return $this->belongsTo(ServiceCatalog::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(PartnerContact::class, 'partner_contact_id');
    }

    public function obligations(): HasMany
    {
        return $this->hasMany(Obligation::class);
    }

    public function licences(): HasMany
    {
        return $this->hasMany(Licence::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }
    public function getExpiresOnFormattedAttribute(): ?string
    {
    return $this->expires_on
        ? $this->expires_on->format('d.m.Y')
        : null;
    }

    public function getDaysRemainingAttribute(): ?int
    {
    if (!$this->expires_on) {
        return null;
    }

    return (int) now()->startOfDay()->diffInDays(
        $this->expires_on->copy()->startOfDay(),
        false
    );
    }
}