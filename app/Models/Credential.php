<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credential extends Model
{
    protected $fillable = [
        'partner_id',
        'asset_id',
        'credential_type',
        'title',
        'username',
        'password',
        'secret_note',
        'url',
        'remote_id',
        'valid_until',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'secret_note' => 'encrypted',
            'valid_until' => 'date',
            'is_active' => 'boolean',
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
}