<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

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

        $partners = $query
            ->orderBy('name')
            ->get();

        return view('partners.index', compact('partners'));
    }

    public function create(Request $request)
    {
        return view('partners.create', [
            'returnTo' => $request->query('return_to'),
            'returnPartnerField' => $request->query('return_partner_field', 'partner_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'oib' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $partner = Partner::create($data);

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
                'is_active' => $partner->is_active,
            ]
        );

        $returnTo = $request->input('return_to');
        $returnPartnerField = $request->input('return_partner_field', 'partner_id');

        if ($returnTo) {
            return redirect()->to($returnTo . '?' . http_build_query([
                $returnPartnerField => $partner->id,
            ]));
        }

        return redirect()->route('partners.index');
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
        ])->findOrFail($id);

        return view('partners.show', compact('partner'));
    }

    public function edit(string $id)
    {
        $partner = Partner::findOrFail($id);

        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'oib' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $partner = Partner::findOrFail($id);
        $before = $partner->fresh()->toArray();

        $partner->update($data);

        $after = $partner->fresh()->toArray();

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
        ]);

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

        return redirect()->route('partners.index');
    }

    public function destroy(string $id)
    {
        $partner = Partner::findOrFail($id);

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
                'is_active' => $partner->is_active,
            ]
        );

        $partner->delete();

        return redirect()->route('partners.index');
    }
}