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

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = filled($value) ? encrypt($value) : null;
    }

    public function getPasswordAttribute($value): ?string
    {
        return filled($value) ? decrypt($value) : null;
    }
}