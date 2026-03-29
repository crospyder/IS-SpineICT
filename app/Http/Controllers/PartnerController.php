<?php

namespace App\Http\Controllers;

use App\Models\ContractServiceType;
use App\Models\Partner;
use App\Services\SudregService;
use App\Support\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    public function index()
    {
        $query = Partner::query();

        if ($q = request('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('legal_name', 'like', "%{$q}%")
                    ->orWhere('oib', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            });
        }

        if (request('active') === '1') {
            $query->where('is_active', 1);
        }

        if (request('active') === '0') {
            $query->where('is_active', 0);
        }

        if (request('contract') === '1') {
            $query->where('is_contract_client', 1);
        }

        if (request('contract') === '0') {
            $query->where('is_contract_client', 0);
        }

        $partners = $query
            ->orderBy('name')
            ->get();

        return view('partners.index', compact('partners'));
    }

    public function create(Request $request)
    {
        $contractServiceTypes = ContractServiceType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('partners.create', [
            'returnTo' => $request->query('return_to'),
            'returnPartnerField' => $request->query('return_partner_field', 'partner_id'),
            'contractServiceTypes' => $contractServiceTypes,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePartner($request);

        $contractServiceIds = $data['contract_service_ids'] ?? [];
        unset($data['contract_service_ids']);

        $partner = Partner::create($data);

        if ($partner->is_contract_client) {
            $partner->contractServices()->sync($contractServiceIds);
        }

        ActivityLogger::log(
            subject: $partner,
            event: 'created',
            entityType: 'partner',
            title: $partner->name,
            newValues: [
                'name' => $partner->name,
                'legal_name' => $partner->legal_name,
                'oib' => $partner->oib,
                'email' => $partner->email,
                'phone' => $partner->phone,
                'website' => $partner->website,
                'address' => $partner->address,
                'city' => $partner->city,
                'postal_code' => $partner->postal_code,
                'country' => $partner->country,
                'notes' => $partner->notes,
                'is_active' => $partner->is_active,

                'is_contract_client' => $partner->is_contract_client,
                'contract_status' => $partner->contract_status,
                'contract_start_date' => optional($partner->contract_start_date)->format('Y-m-d'),
                'contract_end_date' => optional($partner->contract_end_date)->format('Y-m-d'),
                'contract_notes' => $partner->contract_notes,
                'contract_service_ids' => $contractServiceIds,

                'inventory_enabled' => $partner->inventory_enabled,
                'inventory_mode' => $partner->inventory_mode,
                'inventory_partner_key' => $partner->inventory_partner_key,
                'is_internal' => $partner->is_internal,
            ]
        );

        $returnTo = $request->input('return_to');
        $returnPartnerField = $request->input('return_partner_field', 'partner_id');

        if ($returnTo) {
            return redirect()->to($returnTo . '?' . http_build_query([
                $returnPartnerField => $partner->id,
            ]));
        }

        return redirect()
            ->route('partners.edit', $partner)
            ->with('status', 'Partner je uspješno kreiran.');
    }

    public function show(string $id)
    {
        $partner = Partner::with([
            'contacts' => function ($query) {
                $query->orderByDesc('is_primary')->orderBy('name');
            },
            'credentials' => function ($query) {
                $query->orderBy('title');
            },
            'services' => function ($query) {
                $query->orderBy('expires_on')->orderBy('name');
            },
            'obligations' => function ($query) {
                $query->orderByRaw("
                    CASE
                        WHEN completed_date IS NULL AND due_date IS NOT NULL THEN 0
                        ELSE 1
                    END
                ")->orderBy('due_date');
            },
            'contractServices' => function ($query) {
                $query->orderBy('sort_order')->orderBy('name');
            },
            'inventoryItems' => function ($query) {
                $query->orderByDesc('last_seen_at')->orderBy('hostname');
            },
        ])->findOrFail($id);

        return view('partners.show', compact('partner'));
    }

    public function edit(string $id)
    {
        $partner = Partner::with('contractServices')->findOrFail($id);

        $contractServiceTypes = ContractServiceType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('partners.edit', compact('partner', 'contractServiceTypes'));
    }

    public function update(Request $request, string $id)
    {
        $partner = Partner::with('contractServices')->findOrFail($id);
        $data = $this->validatePartner($request, $partner);

        $contractServiceIds = $data['contract_service_ids'] ?? [];
        unset($data['contract_service_ids']);

        $before = $partner->fresh()->load('contractServices')->toArray();
        $beforeContractServiceIds = $partner->contractServices
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        $partner->update($data);

        if ($partner->is_contract_client) {
            $partner->contractServices()->sync($contractServiceIds);
        } else {
            $partner->contractServices()->detach();
            $contractServiceIds = [];
        }

        $partner->load('contractServices');
        $after = $partner->fresh()->load('contractServices')->toArray();

        [$oldValues, $newValues] = ActivityLogger::diff($before, $after, [
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

            'inventory_enabled',
            'inventory_mode',
            'inventory_partner_key',
            'is_internal',
        ]);

        $afterContractServiceIds = $partner->contractServices
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        if ($beforeContractServiceIds !== $afterContractServiceIds) {
            $oldValues['contract_service_ids'] = $beforeContractServiceIds;
            $newValues['contract_service_ids'] = $afterContractServiceIds;
        }

        if (!empty($newValues)) {
            $event = 'updated';

            if (array_key_exists('is_active', $newValues)) {
                $event = $partner->is_active ? 'activated' : 'deactivated';
            }

            ActivityLogger::log(
                subject: $partner,
                event: $event,
                entityType: 'partner',
                title: $partner->name,
                oldValues: $oldValues,
                newValues: $newValues
            );
        }

        return redirect()
            ->route('partners.edit', $partner)
            ->with('status', 'Partner je uspješno ažuriran.');
    }

    public function destroy(string $id)
    {
        $partner = Partner::with('contractServices')->findOrFail($id);

        ActivityLogger::log(
            subject: $partner,
            event: 'deleted',
            entityType: 'partner',
            title: $partner->name,
            oldValues: [
                'name' => $partner->name,
                'legal_name' => $partner->legal_name,
                'oib' => $partner->oib,
                'email' => $partner->email,
                'phone' => $partner->phone,
                'website' => $partner->website,
                'address' => $partner->address,
                'city' => $partner->city,
                'postal_code' => $partner->postal_code,
                'country' => $partner->country,
                'notes' => $partner->notes,
                'is_active' => $partner->is_active,

                'is_contract_client' => $partner->is_contract_client,
                'contract_status' => $partner->contract_status,
                'contract_start_date' => optional($partner->contract_start_date)->format('Y-m-d'),
                'contract_end_date' => optional($partner->contract_end_date)->format('Y-m-d'),
                'contract_notes' => $partner->contract_notes,
                'contract_service_ids' => $partner->contractServices->pluck('id')->all(),

                'inventory_enabled' => $partner->inventory_enabled,
                'inventory_mode' => $partner->inventory_mode,
                'inventory_partner_key' => $partner->inventory_partner_key,
                'is_internal' => $partner->is_internal,
            ]
        );

        $partner->delete();

        return redirect()->route('partners.index');
    }

    public function lookupByOib(Request $request, SudregService $sudregService): JsonResponse
    {
        $validated = $request->validate([
            'oib' => ['required', 'string'],
        ]);

        $normalizedOib = $this->normalizeOib($validated['oib']);

        try {
            $registryData = $sudregService->lookupByOib($normalizedOib);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $existingPartner = Partner::query()
            ->where('oib', $normalizedOib)
            ->first();

        return response()->json([
            'ok' => true,
            'message' => $existingPartner
                ? 'Partner s tim OIB-om već postoji u sustavu.'
                : 'Podaci su uspješno dohvaćeni iz Sudskog registra.',
            'partner' => $registryData,
            'existing_partner' => $existingPartner ? [
                'id' => $existingPartner->id,
                'name' => $existingPartner->name,
                'edit_url' => route('partners.edit', $existingPartner),
            ] : null,
        ]);
    }

    public function refreshFromSudreg(Partner $partner, SudregService $sudregService): JsonResponse
    {
        if (!$partner->oib) {
            return response()->json([
                'ok' => false,
                'message' => 'Partner nema evidentiran OIB.',
            ], 422);
        }

        try {
            $registryData = $sudregService->lookupByOib($partner->oib);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $before = $partner->fresh()->toArray();

        $updateData = [
            'name' => $registryData['name'] ?? $partner->name,
            'legal_name' => $registryData['legal_name'] ?? $partner->legal_name,
            'oib' => $registryData['oib'] ?? $partner->oib,
            'address' => $registryData['address'] ?? $partner->address,
            'city' => $registryData['city'] ?? $partner->city,
            'postal_code' => $registryData['postal_code'] ?? $partner->postal_code,
            'country' => $registryData['country'] ?? $partner->country,
        ];

        $partner->update($updateData);

        $after = $partner->fresh()->toArray();

        [$oldValues, $newValues] = ActivityLogger::diff($before, $after, [
            'name',
            'legal_name',
            'oib',
            'address',
            'city',
            'postal_code',
            'country',
        ]);

        if (!empty($newValues)) {
            ActivityLogger::log(
                subject: $partner,
                event: 'updated',
                entityType: 'partner',
                title: $partner->name,
                message: 'Partner "' . $partner->name . '" je ažuriran iz Sudskog registra.',
                oldValues: $oldValues,
                newValues: $newValues
            );
        }

        return response()->json([
            'ok' => true,
            'message' => empty($newValues)
                ? 'Nema promjena u službenim podacima.'
                : 'Službeni podaci partnera su osvježeni iz Sudskog registra.',
            'partner' => [
                'name' => $partner->name,
                'legal_name' => $partner->legal_name,
                'oib' => $partner->oib,
                'address' => $partner->address,
                'city' => $partner->city,
                'postal_code' => $partner->postal_code,
                'country' => $partner->country,
            ],
            'changed' => !empty($newValues),
        ]);
    }

    protected function validatePartner(Request $request, ?Partner $partner = null): array
    {
        $normalizedOib = $this->normalizeOib((string) $request->input('oib'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'oib' => [
                'nullable',
                'string',
                'size:11',
                Rule::unique('partners', 'oib')->ignore($partner?->id),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],

            'is_contract_client' => ['nullable', 'boolean'],
            'contract_status' => ['nullable', 'string', Rule::in(['active', 'pending', 'paused', 'expired'])],
            'contract_start_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date'],
            'contract_notes' => ['nullable', 'string'],
            'contract_service_ids' => ['nullable', 'array'],
            'contract_service_ids.*' => ['integer', 'exists:contract_service_types,id'],

            'inventory_enabled' => ['nullable', 'boolean'],
            'inventory_mode' => ['nullable', 'string', Rule::in(['manual', 'agent', 'hybrid'])],
            'inventory_partner_key' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('partners', 'inventory_partner_key')->ignore($partner?->id),
            ],
            'is_internal' => ['nullable', 'boolean'],
        ], [
            'oib.size' => 'OIB mora imati točno 11 znamenki.',
            'oib.unique' => 'Partner s tim OIB-om već postoji.',
            'contract_end_date.after_or_equal' => 'Datum završetka ugovora mora biti isti ili nakon početka ugovora.',
            'inventory_partner_key.unique' => 'Inventory partner key već postoji.',
        ]);

        $data['oib'] = $normalizedOib !== '' ? $normalizedOib : null;
        $data['country'] = $data['country'] ?: 'Hrvatska';
        $data['is_active'] = $request->boolean('is_active', true);

        $data['is_contract_client'] = $request->boolean('is_contract_client', false);
        $data['inventory_enabled'] = $request->boolean('inventory_enabled', false);
        $data['is_internal'] = $request->boolean('is_internal', false);

        if (! $data['is_contract_client']) {
            $data['contract_status'] = null;
            $data['contract_start_date'] = null;
            $data['contract_end_date'] = null;
            $data['contract_notes'] = null;
            $data['contract_service_ids'] = [];
        }

        if (! $data['inventory_enabled']) {
            $data['inventory_mode'] = null;
            $data['inventory_partner_key'] = null;
        }

        return $data;
    }

    protected function normalizeOib(string $oib): string
    {
        return preg_replace('/\D+/', '', $oib) ?? '';
    }
}