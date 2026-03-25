<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Licence extends Model
{
    protected $fillable = [
        'partner_id',
        'asset_id',
        'partner_service_id',
        'name',
        'licence_type',
        'licence_key',
        'assigned_to',
        'status',
        'purchased_on',
        'installed_on',
        'valid_until',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'licence_key' => 'encrypted',
            'purchased_on' => 'date',
            'installed_on' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function partnerService(): BelongsTo
    {
        return $this->belongsTo(PartnerService::class);
    }
}