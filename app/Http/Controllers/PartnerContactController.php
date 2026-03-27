<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerContact;
use Illuminate\Http\Request;

class PartnerContactController extends Controller
{
    public function create(Request $request)
    {
        return view('partner-contacts.create', [
            'partners' => Partner::orderBy('name')->get(),
            'returnTo' => $request->query('return_to'),
            'returnPartnerField' => $request->query('return_partner_field', 'partner_id'),
            'returnContactField' => $request->query('return_contact_field', 'partner_contact_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_primary'] = $request->boolean('is_primary', false);
        $data['is_active'] = $request->boolean('is_active', true);

        $contact = PartnerContact::create($data);

        $returnTo = $request->input('return_to');
        $returnPartnerField = $request->input('return_partner_field', 'partner_id');
        $returnContactField = $request->input('return_contact_field', 'partner_contact_id');

        if ($returnTo) {
            $separator = str_contains($returnTo, '?') ? '&' : '?';

            return redirect()->to($returnTo . $separator . http_build_query([
                $returnPartnerField => $contact->partner_id,
                $returnContactField => $contact->id,
            ]));
        }

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_primary'] = $request->boolean('is_primary', false);
        $data['is_active'] = $request->boolean('is_active', true);

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