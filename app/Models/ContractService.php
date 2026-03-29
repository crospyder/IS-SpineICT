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

    public function getHasChangesAttribute(): bool
    {
        return !empty($this->old_values) || !empty($this->new_values);
    }

    public function getChangeRowsAttribute(): array
    {
        $oldValues = is_array($this->old_values) ? $this->old_values : [];
        $newValues = is_array($this->new_values) ? $this->new_values : [];

        $fields = array_values(array_unique(array_merge(
            array_keys($oldValues),
            array_keys($newValues)
        )));

        $rows = [];

        foreach ($fields as $field) {
            $rows[] = [
                'field' => $field,
                'label' => $this->fieldLabel($field),
                'old' => $this->formatValue($oldValues[$field] ?? null),
                'new' => $this->formatValue($newValues[$field] ?? null),
            ];
        }

        return $rows;
    }

    protected function fieldLabel(string $field): string
    {
        return match ($field) {
            'partner_id' => 'Partner',
            'partner_service_id' => 'Usluga',
            'partner_contact_id' => 'Kontakt',
            'title' => 'Naslov',
            'name' => 'Naziv',
            'legal_name' => 'Pravni naziv',
            'reference_no' => 'Referenca',
            'description' => 'Opis',
            'status' => 'Status',
            'priority' => 'Prioritet',
            'due_date' => 'Rok',
            'completed_date' => 'Završeno',
            'is_recurring' => 'Ponavljanje',
            'recurrence_type' => 'Tip ponavljanja',
            'remind_days_before' => 'Podsjetnik prije',
            'service_type' => 'Tip usluge',
            'provider' => 'Provider',
            'registrar' => 'Registrar',
            'renewal_period' => 'Period obnove',
            'auto_renew' => 'Auto renew',
            'starts_on' => 'Početak',
            'expires_on' => 'Istek',
            'renewal_date' => 'Obnovljeno',
            'renewal_method' => 'Način obnove',
            'is_active' => 'Aktivno',
            'resolved' => 'Riješeno',
            'oib' => 'OIB',
            'email' => 'Email',
            'phone' => 'Telefon',
            'website' => 'Web',
            'address' => 'Adresa',
            'city' => 'Grad',
            'postal_code' => 'Poštanski broj',
            'country' => 'Država',
            'notes' => 'Napomena',
            'offer_date' => 'Datum ponude',
            'valid_until' => 'Vrijedi do',
            'vat_rate' => 'PDV',
            default => str_replace('_', ' ', ucfirst($field)),
        };
    }

    protected function formatValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Da' : 'Ne';
        }

        if (is_numeric($value) && in_array((string) $value, ['0', '1'], true)) {
            return match ((string) $value) {
                '1' => 'Da',
                '0' => 'Ne',
                default => (string) $value,
            };
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $stringValue = (string) $value;

        if (preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/', $stringValue)) {
            try {
                return \Carbon\Carbon::parse($stringValue)->format('d.m.Y H:i');
            } catch (\Throwable $e) {
                return $stringValue;
            }
        }

        return $stringValue;
    }
}