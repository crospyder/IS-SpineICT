<?php

namespace App\Console\Commands;

use App\Models\Partner;
use App\Services\SudregService;
use Illuminate\Console\Command;

class SyncPartnersFromSudreg extends Command
{
    protected $signature = 'partners:sync-sudreg
                            {--partner_id= : Sync samo jednog partnera po ID-u}
                            {--only-empty : Sync samo partnere koji imaju prazne službene podatke}';

    protected $description = 'Batch sync partnera iz Sudskog registra po OIB-u';

    public function handle(SudregService $sudregService): int
    {
        $query = Partner::query()
            ->whereNotNull('oib')
            ->orderBy('name');

        if ($partnerId = $this->option('partner_id')) {
            $query->where('id', $partnerId);
        }

        $partners = $query->get();

        if ($partners->isEmpty()) {
            $this->warn('Nema partnera za sync.');
            return self::SUCCESS;
        }

        $updated = 0;
        $unchanged = 0;
        $skipped = 0;
        $failed = 0;

        $this->info("Pokrećem Sudreg sync za {$partners->count()} partnera...");
        $this->newLine();

        $bar = $this->output->createProgressBar($partners->count());
        $bar->start();

        foreach ($partners as $partner) {
            try {
                $normalizedOib = preg_replace('/\D+/', '', (string) $partner->oib) ?? '';

                if (strlen($normalizedOib) !== 11) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                if ($this->option('only-empty') && !$this->hasEmptyOfficialFields($partner)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $registryData = $sudregService->lookupByOib($normalizedOib);

                $updateData = [
                    'name' => $registryData['name'] ?? $partner->name,
                    'legal_name' => $registryData['legal_name'] ?? $partner->legal_name,
                    'oib' => $registryData['oib'] ?? $partner->oib,
                    'address' => $registryData['address'] ?? $partner->address,
                    'city' => $registryData['city'] ?? $partner->city,
                    'postal_code' => $registryData['postal_code'] ?? $partner->postal_code,
                    'country' => $registryData['country'] ?? $partner->country,
                ];

                $before = [
                    'name' => $partner->name,
                    'legal_name' => $partner->legal_name,
                    'oib' => $partner->oib,
                    'address' => $partner->address,
                    'city' => $partner->city,
                    'postal_code' => $partner->postal_code,
                    'country' => $partner->country,
                ];

                $changed = $this->hasChanges($before, $updateData);

                if ($changed) {
                    $partner->update($updateData);
                    $updated++;
                } else {
                    $unchanged++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Greška za partnera #{$partner->id} {$partner->name}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['updated', 'unchanged', 'skipped', 'failed'],
            [[$updated, $unchanged, $skipped, $failed]]
        );

        return self::SUCCESS;
    }

    protected function hasEmptyOfficialFields(Partner $partner): bool
    {
        foreach ([
            $partner->name,
            $partner->legal_name,
            $partner->address,
            $partner->city,
            $partner->postal_code,
            $partner->country,
        ] as $value) {
            if ($value === null || trim((string) $value) === '') {
                return true;
            }
        }

        return false;
    }

    protected function hasChanges(array $before, array $after): bool
    {
        foreach ($after as $key => $value) {
            $beforeValue = $before[$key] ?? null;

            if ($this->normalizeValue($beforeValue) !== $this->normalizeValue($value)) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }
}