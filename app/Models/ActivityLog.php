<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'event',
        'entity_type',
        'title',
        'message',
        'old_values',
        'new_values',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEntityLabelAttribute(): string
    {
        return match ($this->entity_type) {
            'service' => 'Usluga',
            'obligation' => 'Obveza',
            'procurement' => 'Kalkulacija',
            'partner' => 'Partner',
            default => 'Aktivnost',
        };
    }
}