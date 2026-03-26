<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Obligation extends Model
{
    protected $fillable = [
        'partner_id',
        'partner_service_id',
        'partner_contact_id',
        'asset_id',
        'assigned_user_id',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'due_date',
        'completed_date',
        'is_recurring',
        'recurrence_type',
        'remind_days_before',
        'last_reminder_sent_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_date' => 'date',
            'is_recurring' => 'boolean',
            'last_reminder_sent_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function partnerService(): BelongsTo
    {
        return $this->belongsTo(PartnerService::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(PartnerContact::class, 'partner_contact_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeExpiringSoon(Builder $query): Builder
    {
        return $query
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', Carbon::now()->addDays(7))
            ->whereDate('due_date', '>=', Carbon::now());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::now());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('completed_date');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->completed_date;
    }

    public function isExpiringSoon(): bool
    {
        return $this->due_date &&
            $this->due_date->isFuture() &&
            $this->due_date->lte(Carbon::now()->addDays(7)) &&
            !$this->completed_date;
    }

    public function isCompleted(): bool
    {
    return $this->status === 'done' || $this->completed_date !== null;
    }
}