<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityLogger
{
    protected static ?array $columns = null;

    public static function log(
        Model $subject,
        string $event,
        ?string $entityType = null,
        ?string $title = null,
        ?string $message = null,
        array $oldValues = [],
        array $newValues = []
    ): void {
        if (!Schema::hasTable('activity_logs')) {
            return;
        }

        $columns = self::columns();

        if (empty($columns)) {
            return;
        }

        $entityType ??= self::detectEntityType($subject);
        $displayTitle = self::resolveTitle($title, $subject, $entityType);
        $displayMessage = $message ?: self::buildMessage($entityType, $event, $displayTitle);

        $payload = [];

        if (in_array('user_id', $columns, true)) {
            $payload['user_id'] = Auth::id();
        }

        if (in_array('subject_type', $columns, true)) {
            $payload['subject_type'] = $subject::class;
        }

        if (in_array('subject_id', $columns, true)) {
            $payload['subject_id'] = $subject->getKey();
        }

        if (in_array('event', $columns, true)) {
            $payload['event'] = $event;
        }

        if (in_array('entity_type', $columns, true)) {
            $payload['entity_type'] = $entityType;
        }

        if (in_array('title', $columns, true)) {
            $payload['title'] = self::buildActivityTitle($entityType, $event);
        }

        if (in_array('message', $columns, true)) {
            $payload['message'] = $displayMessage;
        }

        if (in_array('old_values', $columns, true)) {
            $payload['old_values'] = empty($oldValues) ? null : json_encode($oldValues, JSON_UNESCAPED_UNICODE);
        }

        if (in_array('new_values', $columns, true)) {
            $payload['new_values'] = empty($newValues) ? null : json_encode($newValues, JSON_UNESCAPED_UNICODE);
        }

        if (in_array('created_at', $columns, true)) {
            $payload['created_at'] = now();
        }

        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        if (empty($payload)) {
            return;
        }

        DB::table('activity_logs')->insert($payload);
    }

    public static function diff(array $old, array $new, array $fields): array
    {
        $oldValues = [];
        $newValues = [];

        foreach ($fields as $field) {
            $oldValue = $old[$field] ?? null;
            $newValue = $new[$field] ?? null;

            if (self::normalize($oldValue) !== self::normalize($newValue)) {
                $oldValues[$field] = $oldValue;
                $newValues[$field] = $newValue;
            }
        }

        return [$oldValues, $newValues];
    }

    protected static function detectEntityType(Model $subject): string
    {
        return match (class_basename($subject)) {
            'Obligation' => 'obligation',
            'PartnerService' => 'service',
            'Procurement' => 'procurement',
            'Partner' => 'partner',
            default => 'activity',
        };
    }

    protected static function resolveTitle(?string $title, Model $subject, ?string $entityType): string
    {
        if ($title !== null && trim($title) !== '') {
            return trim($title);
        }

        foreach (['title', 'name', 'reference_no'] as $attribute) {
            $value = $subject->getAttribute($attribute);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return self::entityLabel($entityType);
    }

    protected static function buildActivityTitle(?string $entityType, string $event): string
    {
        $entityLabel = self::entityLabel($entityType);
        $eventLabel = self::eventLabel($event);

        return trim($entityLabel . ' ' . $eventLabel);
    }

    protected static function buildMessage(?string $entityType, string $event, string $title): string
    {
        $entityLabel = self::entityLabel($entityType);

        return match ($event) {
            'created' => $entityLabel . ' "' . $title . '" je kreiran.',
            'updated' => $entityLabel . ' "' . $title . '" je ažuriran.',
            'deleted' => $entityLabel . ' "' . $title . '" je obrisan.',
            'completed' => $entityLabel . ' "' . $title . '" je dovršen.',
            'renewed' => $entityLabel . ' "' . $title . '" je produljen.',
            'activated' => $entityLabel . ' "' . $title . '" je aktiviran.',
            'deactivated' => $entityLabel . ' "' . $title . '" je deaktiviran.',
            default => $entityLabel . ' "' . $title . '" je promijenjen.',
        };
    }

    protected static function entityLabel(?string $entityType): string
    {
        return match ($entityType) {
            'service' => 'Usluga',
            'obligation' => 'Obveza',
            'procurement' => 'Kalkulacija',
            'partner' => 'Partner',
            default => 'Aktivnost',
        };
    }

    protected static function eventLabel(string $event): string
    {
        return match ($event) {
            'created' => 'kreirana',
            'updated' => 'ažurirana',
            'deleted' => 'obrisana',
            'completed' => 'dovršena',
            'renewed' => 'produljena',
            'activated' => 'aktivirana',
            'deactivated' => 'deaktivirana',
            default => 'promijenjena',
        };
    }

    protected static function normalize(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }

    protected static function columns(): array
    {
        if (self::$columns !== null) {
            return self::$columns;
        }

        self::$columns = Schema::getColumnListing('activity_logs');

        return self::$columns;
    }
}