<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'legal_name',
        'oib',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'postal_code',
        'country',
        'notes',
        'is_active',
        'is_contract_client',
        'contract_status',
        'contract_start_date',
        'contract_end_date',
        'contract_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_contract_client' => 'boolean',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PartnerContact::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(PartnerService::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function obligations(): HasMany
    {
        return $this->hasMany(Obligation::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
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

    public function contractServices(): BelongsToMany
    {
        return $this->belongsToMany(
            ContractServiceType::class,
            'partner_contract_services'
        )->withTimestamps();
    }

    public function hasContractService(string $slug): bool
    {
        if (! $this->relationLoaded('contractServices')) {
            $this->load('contractServices');
        }

        return $this->contractServices->contains('slug', $slug);
    }
}