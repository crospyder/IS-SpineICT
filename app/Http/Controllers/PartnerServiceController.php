<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerService;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class PartnerServiceController extends Controller
{
    public function index()
    {
        $query = PartnerService::with('partner');

        if ($q = request('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('service_type', 'like', "%{$q}%")
                    ->orWhere('domain_name', 'like', "%{$q}%")
                    ->orWhere('provider', 'like', "%{$q}%")
                    ->orWhere('registrar', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%")
                    ->orWhereHas('partner', function ($partnerSub) use ($q) {
                        $partnerSub->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($partnerId = request('partner_id')) {
            $query->where('partner_id', $partnerId);
        }

        if (request('expiring')) {
            $query->whereNotNull('expires_on')
                ->whereDate('expires_on', '<=', now()->addDays(30));
        }

        if (request('active') === '1') {
            $query->where('is_active', true);
        }

        if (request('active') === '0') {
            $query->where('is_active', false);
        }

        $partnerServices = $query
            ->orderByRaw('CASE WHEN expires_on IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expires_on')
            ->orderBy('name')
            ->get();

        $partners = Partner::orderBy('name')->get();

        return view('partner-services.index', compact('partnerServices', 'partners'));
    }

    public function create()
    {
        $partners = Partner::orderBy('name')->get();

        return view('partner-services.create', compact('partners'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'service_type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'domain_name' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
            'registrar' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'renewal_period' => 'nullable|string|max:50',
            'auto_renew' => 'nullable|boolean',
            'starts_on' => 'nullable|date',
            'expires_on' => 'nullable|date',
            'renewal_date' => 'nullable|date',
            'admin_link' => 'nullable|string|max:255',
            'renewal_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['auto_renew'] = $request->boolean('auto_renew');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['status'] = $data['is_active'] ? 'active' : 'inactive';
        $data['resolved'] = false;

        $service = PartnerService::create($data);

        ActivityLogger::log(
            subject: $service,
            event: 'created',
            entityType: 'service',
            title: $service->name,
            newValues: [
                'partner_id' => $service->partner_id,
                'name' => $service->name,
                'service_type' => $service->service_type,
                'provider' => $service->provider,
                'registrar' => $service->registrar,
                'status' => $service->status,
                'renewal_period' => $service->renewal_period,
                'auto_renew' => $service->auto_renew,
                'starts_on' => optional($service->starts_on)->toDateString(),
                'expires_on' => optional($service->expires_on)->toDateString(),
                'renewal_date' => optional($service->renewal_date)->toDateString(),
                'is_active' => $service->is_active,
                'resolved' => $service->resolved,
            ]
        );

        return redirect()
            ->route('partner-services.index')
            ->with('success', 'Usluga je spremljena.');
    }

    public function show(string $id)
    {
        $partnerService = PartnerService::with('partner')->findOrFail($id);

        return view('partner-services.show', compact('partnerService'));
    }

    public function edit(string $id)
    {
        $partnerService = PartnerService::findOrFail($id);
        $partners = Partner::orderBy('name')->get();

        return view('partner-services.edit', compact('partnerService', 'partners'));
    }

    public function update(Request $request, string $id)
    {
        $partnerService = PartnerService::findOrFail($id);

        if ($request->boolean('renew_action')) {
            $oldExpiry = optional($partnerService->expires_on)->toDateString();
            $newExpiry = $partnerService->calculateRenewedExpirationDate();

            if (!$newExpiry) {
                return redirect()
                    ->route('partner-services.index')
                    ->withErrors([
                        'renewal_period' => 'Usluga nema prepoznat period obnove pa se ne može automatski produljiti.',
                    ]);
            }

            $partnerService->update([
                'expires_on' => $newExpiry->toDateString(),
                'renewal_date' => now()->toDateString(),
                'resolved' => false,
                'is_active' => true,
                'status' => 'active',
            ]);

            ActivityLogger::log(
                subject: $partnerService,
                event: 'renewed',
                entityType: 'service',
                title: $partnerService->name,
                oldValues: [
                    'expires_on' => $oldExpiry,
                ],
                newValues: [
                    'expires_on' => $newExpiry->format('Y-m-d'),
                    'renewal_date' => now()->toDateString(),
                    'status' => 'active',
                    'is_active' => true,
                    'resolved' => false,
                ]
            );

            return redirect()
                ->route('partner-services.index')
                ->with('success', 'Usluga je produljena.');
        }

        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'service_type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'domain_name' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
            'registrar' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'renewal_period' => 'nullable|string|max:50',
            'auto_renew' => 'nullable|boolean',
            'starts_on' => 'nullable|date',
            'expires_on' => 'nullable|date',
            'renewal_date' => 'nullable|date',
            'admin_link' => 'nullable|string|max:255',
            'renewal_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $before = $partnerService->fresh()->toArray();

        $data['auto_renew'] = $request->boolean('auto_renew');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['status'] = $data['is_active'] ? 'active' : 'inactive';

        if (!$data['is_active']) {
            $data['resolved'] = true;
        } elseif ($partnerService->resolved) {
            $data['resolved'] = false;
        }

        $partnerService->update($data);

        $after = $partnerService->fresh()->toArray();

        [$oldValues, $newValues] = ActivityLogger::diff($before, $after, [
            'partner_id',
            'name',
            'service_type',
            'provider',
            'registrar',
            'status',
            'renewal_period',
            'auto_renew',
            'starts_on',
            'expires_on',
            'renewal_date',
            'renewal_method',
            'is_active',
            'resolved',
        ]);

        if (!empty($newValues)) {
            $event = 'updated';

            if (array_key_exists('is_active', $newValues)) {
                $event = $partnerService->is_active ? 'activated' : 'deactivated';
            }

            ActivityLogger::log(
                subject: $partnerService,
                event: $event,
                entityType: 'service',
                title: $partnerService->name,
                oldValues: $oldValues,
                newValues: $newValues
            );
        }

        return redirect()
            ->route('partner-services.index')
            ->with('success', 'Usluga je ažurirana.');
    }

    public function destroy(string $id)
    {
        $partnerService = PartnerService::findOrFail($id);

        ActivityLogger::log(
            subject: $partnerService,
            event: 'deleted',
            entityType: 'service',
            title: $partnerService->name,
            oldValues: [
                'partner_id' => $partnerService->partner_id,
                'name' => $partnerService->name,
                'service_type' => $partnerService->service_type,
                'provider' => $partnerService->provider,
                'registrar' => $partnerService->registrar,
                'status' => $partnerService->status,
                'expires_on' => optional($partnerService->expires_on)->toDateString(),
                'is_active' => $partnerService->is_active,
                'resolved' => $partnerService->resolved,
            ]
        );

        $partnerService->delete();

        return redirect()
            ->route('partner-services.index')
            ->with('success', 'Usluga je obrisana.');
    }
}