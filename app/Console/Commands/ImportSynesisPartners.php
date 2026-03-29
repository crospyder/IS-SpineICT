<?php

namespace App\Console\Commands;

use App\Models\Partner;
use Illuminate\Console\Command;

class ImportSynesisPartners extends Command
{
    protected $signature = 'partners:import-synesis
                            {file : Putanja do CSV filea}
                            {--delimiter=; : CSV delimiter}
                            {--skip-invalid-oib : Preskoči retke bez valjanog OIB-a}';

    protected $description = 'Jednokratni import partnera iz Synesis CSV izvoza';

    public function handle(): int
    {
        $filePath = $this->argument('file');
        $delimiter = (string) $this->option('delimiter');
        $skipInvalidOib = (bool) $this->option('skip-invalid-oib');

        if (!is_file($filePath)) {
            $this->error("CSV file ne postoji: {$filePath}");
            return self::FAILURE;
        }

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            $this->error("Ne mogu otvoriti CSV file: {$filePath}");
            return self::FAILURE;
        }

        $header = fgetcsv($handle, 0, $delimiter);

        if ($header === false) {
            fclose($handle);
            $this->error('CSV je prazan ili nečitljiv.');
            return self::FAILURE;
        }

        $header = $this->normalizeHeader($header);

        $requiredColumns = [
            'SIFRA',
            'NAZIV_PARTNERA',
            'HP_BROJ',
            'MJESTO',
            'ULICA_I_KBR',
            'NAZIV_DRZAVE',
            'TELEFON',
            'E_MAIL',
            'PDV_ID_BR_OIB',
            'DIN',
        ];

        foreach ($requiredColumns as $column) {
            if (!in_array($column, $header, true)) {
                fclose($handle);
                $this->error("Nedostaje očekivani stupac: {$column}");
                return self::FAILURE;
            }
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $warnings = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $data = $this->combineRow($header, $row);
            $name = $this->cleanString($data['NAZIV_PARTNERA'] ?? null);

            if (!$name) {
                $skipped++;
                $this->warn("Red {$rowNumber}: preskočen jer nema naziv partnera.");
                continue;
            }

            $rawOib = $this->cleanString($data['PDV_ID_BR_OIB'] ?? null);
            $normalizedOib = $this->normalizeDigits($rawOib);
            $validOib = strlen($normalizedOib) === 11 ? $normalizedOib : null;

            if (!$validOib && $rawOib !== null && $rawOib !== '') {
                $warnings++;
                $this->warn("Red {$rowNumber}: neispravan OIB/VAT '{$rawOib}' za partnera '{$name}'.");
            }

            if (!$validOib && $skipInvalidOib) {
                $skipped++;
                continue;
            }

            $payload = [
                'name' => $name,
                'legal_name' => $name,
                'oib' => $validOib,
                'email' => $this->cleanString($data['E_MAIL'] ?? null),
                'phone' => $this->cleanString($data['TELEFON'] ?? null),
                'website' => null,
                'address' => $this->cleanString($data['ULICA_I_KBR'] ?? null),
                'city' => $this->cleanString($data['MJESTO'] ?? null),
                'postal_code' => $this->cleanPostalCode($data['HP_BROJ'] ?? null),
                'country' => $this->normalizeCountry($data['NAZIV_DRZAVE'] ?? null),
                'notes' => $this->buildNotes(
                    sifra: $this->cleanString($data['SIFRA'] ?? null),
                    din: $this->cleanString($data['DIN'] ?? null)
                ),
                'is_active' => true,
            ];

            if ($validOib) {
                $existing = Partner::where('oib', $validOib)->first();

                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                    $this->line("Ažuriran: {$name} [OIB {$validOib}]");
                } else {
                    Partner::create($payload);
                    $created++;
                    $this->line("Kreiran: {$name} [OIB {$validOib}]");
                }

                continue;
            }

            $existingByName = Partner::where('name', $name)->first();

            if ($existingByName && empty($existingByName->oib)) {
                $existingByName->update($payload);
                $updated++;
                $this->line("Ažuriran po nazivu: {$name}");
            } else {
                Partner::create($payload);
                $created++;
                $this->line("Kreiran bez OIB-a: {$name}");
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info('Import završen.');
        $this->table(
            ['created', 'updated', 'skipped', 'warnings'],
            [[$created, $updated, $skipped, $warnings]]
        );

        return self::SUCCESS;
    }

    protected function normalizeHeader(array $header): array
    {
        return array_map(function ($value) {
            $value = (string) $value;
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);

            return trim($value);
        }, $header);
    }

    protected function combineRow(array $header, array $row): array
    {
        $row = array_pad($row, count($header), null);

        return array_combine($header, $row) ?: [];
    }

    protected function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function cleanString(?string $value): ?string
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value !== '' ? $value : null;
    }

    protected function normalizeDigits(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?? '';
    }

    protected function cleanPostalCode(?string $value): ?string
    {
        $value = $this->cleanString($value);

        if ($value === null) {
            return null;
        }

        if (mb_strtolower($value) === 'nema') {
            return null;
        }

        return $value;
    }

    protected function normalizeCountry(?string $value): string
    {
        $value = $this->cleanString($value);

        if (!$value) {
            return 'Hrvatska';
        }

        return match (mb_strtoupper($value)) {
            'HRVATSKA' => 'Hrvatska',
            default => $value,
        };
    }

    protected function buildNotes(?string $sifra, ?string $din): ?string
    {
        $parts = ['Imported from Synesis'];

        if ($sifra) {
            $parts[] = "Šifra: {$sifra}";
        }

        if ($din) {
            $parts[] = "DIN: {$din}";
        }

        return implode(' | ', $parts);
    }
}