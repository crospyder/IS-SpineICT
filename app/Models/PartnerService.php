<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function isRecurring(): bool
    {
        return !empty($this->renewal_period) || $this->auto_renew;
    }

    public function canBeRenewed(): bool
    {
        return $this->parseRenewalPeriod() !== null;
    }

    public function getRenewalLabelAttribute(): string
    {
        if (!$this->isRecurring()) {
            return 'Jednokratna';
        }

        $parsed = $this->parseRenewalPeriod();

        if (!$parsed) {
            return $this->auto_renew ? 'Ponavljajuća' : ($this->renewal_period ?: 'Ponavljajuća');
        }

        [$amount, $unit] = $parsed;

        $unitLabel = match ($unit) {
            'day' => $amount === 1 ? 'dan' : 'dana',
            'week' => $amount === 1 ? 'tjedan' : 'tjedna',
            'month' => $amount === 1 ? 'mjesec' : 'mjeseci',
            'year' => $amount === 1 ? 'godina' : 'godina',
            default => $unit,
        };

        return trim($amount . ' ' . $unitLabel);
    }

    public function getLifecycleStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Neaktivna';
        }

        if ($this->resolved) {
            return 'Riješena';
        }

        return 'Aktivna';
    }

    public function getLifecycleStatusClassAttribute(): string
    {
        if (!$this->is_active) {
            return 'app-badge';
        }

        if ($this->resolved) {
            return 'app-badge badge-ok';
        }

        return 'app-badge badge-ok';
    }

    public function getExpiryBadgeClassAttribute(): string
    {
        if (!$this->expires_on) {
            return 'app-badge';
        }

        if ($this->expires_on->isBefore(now()->startOfDay())) {
            return 'app-badge badge-overdue';
        }

        if ($this->expires_on->lte(now()->addDays(30)->startOfDay())) {
            return 'app-badge badge-soon';
        }

        return 'app-badge badge-ok';
    }

    public function getExpiryLabelAttribute(): string
    {
        if (!$this->expires_on) {
            return '-';
        }

        return $this->expires_on->format('Y-m-d');
    }

    public function calculateRenewedExpirationDate(): ?Carbon
    {
        $parsed = $this->parseRenewalPeriod();

        if (!$parsed) {
            return null;
        }

        [$amount, $unit] = $parsed;

        $baseDate = $this->expires_on
            ? $this->expires_on->copy()
            : now()->startOfDay();

        return match ($unit) {
            'day' => $baseDate->addDays($amount),
            'week' => $baseDate->addWeeks($amount),
            'month' => $baseDate->addMonths($amount),
            'year' => $baseDate->addYears($amount),
            default => null,
        };
    }

    protected function parseRenewalPeriod(): ?array
    {
        $value = trim(mb_strtolower((string) $this->renewal_period));

        if ($value === '') {
            return null;
        }

        $normalized = str_replace(
            ['č', 'ć', 'ž', 'š', 'đ'],
            ['c', 'c', 'z', 's', 'd'],
            $value
        );

        if (in_array($normalized, ['godisnje', 'godisnja', 'godisnji', 'godina', 'yearly', 'annual', 'year'])) {
            return [1, 'year'];
        }

        if (in_array($normalized, ['mjesecno', 'mjesecna', 'mjesecni', 'mjesec', 'monthly', 'month'])) {
            return [1, 'month'];
        }

        if (in_array($normalized, ['tjedno', 'tjedna', 'tjedni', 'tjedan', 'weekly', 'week'])) {
            return [1, 'week'];
        }

        if (in_array($normalized, ['dnevno', 'dnevna', 'dnevni', 'dan', 'daily', 'day'])) {
            return [1, 'day'];
        }

        if (preg_match('/^(\d+)\s*(dan|dana|day|days)$/', $normalized, $m)) {
            return [(int) $m[1], 'day'];
        }

        if (preg_match('/^(\d+)\s*(tjedan|tjedna|week|weeks)$/', $normalized, $m)) {
            return [(int) $m[1], 'week'];
        }

        if (preg_match('/^(\d+)\s*(mjesec|mjeseca|mjeseci|month|months)$/', $normalized, $m)) {
            return [(int) $m[1], 'month'];
        }

        if (preg_match('/^(\d+)\s*(godina|godine|god|year|years)$/', $normalized, $m)) {
            return [(int) $m[1], 'year'];
        }

        return null;
    }
}