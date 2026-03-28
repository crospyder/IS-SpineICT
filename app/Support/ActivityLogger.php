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
        string $message,
        ?string $entityType = null,
        ?string $title = null,
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
            $payload['title'] = $title;
        }

        if (in_array('message', $columns, true)) {
            $payload['message'] = $message;
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