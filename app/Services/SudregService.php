<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SudregService
{
    public function lookupByOib(string $oib): array
    {
        $normalizedOib = $this->normalizeOib($oib);

        if (strlen($normalizedOib) !== 11) {
            throw new RuntimeException('OIB mora imati točno 11 znamenki.');
        }

        $response = Http::acceptJson()
            ->withToken($this->getAccessToken())
            ->get($this->baseUrl() . '/subjekt_detalji', [
                'identifikator' => $normalizedOib,
                'tipIdentifikatora' => 'oib',
            ]);

        if ($response->status() === 404) {
            throw new RuntimeException('Subjekt nije pronađen u Sudskom registru za zadani OIB.');
        }

        if ($response->failed()) {
            throw new RuntimeException('Greška pri dohvaćanju podataka iz Sudskog registra.');
        }

        $data = $response->json();

        return $this->mapToPartnerData($normalizedOib, is_array($data) ? $data : []);
    }

    protected function getAccessToken(): string
    {
        return Cache::remember('sudreg_access_token', now()->addMinutes(50), function () {
            $clientId = config('services.sudreg.client_id');
            $clientSecret = config('services.sudreg.client_secret');
            $tokenUrl = config('services.sudreg.token_url', 'https://sudreg-data.gov.hr/api/oauth/token');

            if (!$clientId || !$clientSecret) {
                throw new RuntimeException('Sudreg API kredencijali nisu postavljeni u .env.');
            }

            $response = Http::asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->failed()) {
                throw new RuntimeException('Nije moguće dohvatiti OAuth token iz Sudskog registra.');
            }

            $token = $response->json('access_token');

            if (!$token) {
                throw new RuntimeException('OAuth token nije vraćen od Sudreg API-ja.');
            }

            return $token;
        });
    }

    protected function mapToPartnerData(string $oib, array $data): array
    {
        $legalName = data_get($data, 'tvrtke.0.ime');

// pokušaj dohvatiti skraćeni naziv (ovisno o API strukturi)
$shortName =
    data_get($data, 'tvrtke.0.skraceno_ime') ??
    data_get($data, 'tvrtke.0.skraceni_naziv') ??
    data_get($data, 'tvrtke.0.tvrtka') ??
    null;

// fallback: generiraj skraćeni naziv iz dugog
if (!$shortName && $legalName) {
    $shortName = $this->makeShortName($legalName);
}
        $seat = data_get($data, 'sjedista.0', []);

        $address = trim(implode(' ', array_filter([
            data_get($seat, 'ulica'),
            data_get($seat, 'kucni_broj'),
        ], fn ($value) => filled($value))));

        $city = data_get($seat, 'naziv_naselja')
            ?? data_get($seat, 'naziv_mjesta')
            ?? data_get($seat, 'mjesto');

        $postalCode = data_get($seat, 'postanski_broj')
            ?? data_get($seat, 'broj_poste')
            ?? data_get($seat, 'posta');

        return [
    'oib' => $oib,
    'name' => $shortName ?: $legalName ?: $oib,
    'legal_name' => $legalName,
            'address' => $address ?: null,
            'city' => $city ?: null,
            'postal_code' => $postalCode ? (string) $postalCode : null,
            'country' => 'Hrvatska',
            'raw' => $data,
        ];
    }
    protected function makeShortName(string $name): string
{
    // standardne zamjene
    $replacements = [
        'jednostavno društvo s ograničenom odgovornošću' => 'j.d.o.o.',
        'društvo s ograničenom odgovornošću' => 'd.o.o.',
        'dioničko društvo' => 'd.d.',
    ];

    $short = mb_strtolower($name);

    foreach ($replacements as $long => $abbr) {
        if (str_contains($short, $long)) {
            $short = str_ireplace($long, $abbr, $name);
            break;
        }
    }

    // cleanup: višestruki razmaci
    $short = preg_replace('/\s+/', ' ', $short);

    return trim($short);
}
    protected function normalizeOib(string $oib): string
    {
        return preg_replace('/\D+/', '', $oib) ?? '';
    }

    protected function baseUrl(): string
    {
        return rtrim(
            config('services.sudreg.base_url', 'https://sudreg-data.gov.hr/api/javni/v1'),
            '/'
        );
    }
}