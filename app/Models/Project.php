<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'partner_id',
        'partner_contact_id',
        'owner_user_id',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'planned_cost_total',
        'planned_sell_total',
        'actual_cost_total',
        'actual_sell_total',
        'currency',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'planned_cost_total' => 'decimal:2',
            'planned_sell_total' => 'decimal:2',
            'actual_cost_total' => 'decimal:2',
            'actual_sell_total' => 'decimal:2',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(PartnerContact::class, 'partner_contact_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }
}