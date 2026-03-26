<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerService;
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
                    ->orWhere('status', 'like', "%{$q}%");
            })->orWhereHas('partner', function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%");
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

        PartnerService::create($data);

        return redirect()->route('partner-services.index');
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

        $partnerService = PartnerService::findOrFail($id);
        $partnerService->update($data);

        return redirect()->route('partner-services.show', $partnerService);
    }

    public function destroy(string $id)
    {
        $partnerService = PartnerService::findOrFail($id);
        $partnerService->delete();

        return redirect()->route('partner-services.index');
    }
}