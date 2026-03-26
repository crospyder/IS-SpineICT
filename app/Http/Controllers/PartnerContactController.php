<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerContact;
use Illuminate\Http\Request;

class PartnerContactController extends Controller
{
    public function create()
    {
        return view('partner-contacts.create', [
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
        ]);

        $data['is_primary'] = $request->boolean('is_primary', false);

        PartnerContact::create($data);

        return redirect()->route('partners.show', $data['partner_id']);
    }

    public function edit(string $id)
    {
        $item = PartnerContact::findOrFail($id);

        return view('partner-contacts.edit', [
            'item' => $item,
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
        ]);

        $data['is_primary'] = $request->boolean('is_primary', false);

        $item = PartnerContact::findOrFail($id);
        $item->update($data);

        return redirect()->route('partners.show', $data['partner_id']);
    }

    public function destroy(string $id)
    {
        $item = PartnerContact::findOrFail($id);
        $partnerId = $item->partner_id;

        $item->delete();

        return redirect()->route('partners.show', $partnerId);
    }
}