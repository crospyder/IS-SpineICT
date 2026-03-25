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

    // filter: ističe unutar 30 dana
    if (request('expiring')) {
        $query->whereNotNull('expires_on')
              ->whereDate('expires_on', '<=', now()->addDays(30));
    }

    $items = $query->orderBy('expires_on')->get();

    return view('partner_services.index', compact('items'));
}

    public function create()
    {
        $partners = Partner::orderBy('name')->get();
        return view('partner_services.create', compact('partners'));
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

        return redirect('/partner-services');
    }

    public function show(string $id)
    {
        $item = PartnerService::with('partner')->findOrFail($id);
        return view('partner_services.show', compact('item'));
    }

    public function edit(string $id)
    {
        $item = PartnerService::findOrFail($id);
        $partners = Partner::orderBy('name')->get();

        return view('partner_services.edit', compact('item', 'partners'));
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

        $item = PartnerService::findOrFail($id);
        $item->update($data);

        return redirect('/partner-services/' . $item->id);
    }

    public function destroy(string $id)
    {
        $item = PartnerService::findOrFail($id);
        $item->delete();

        return redirect('/partner-services');
    }
}