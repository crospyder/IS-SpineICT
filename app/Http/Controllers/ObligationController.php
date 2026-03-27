<?php

namespace App\Http\Controllers;

use App\Models\Obligation;
use App\Models\Partner;
use App\Models\PartnerContact;
use App\Models\PartnerService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ObligationController extends Controller
{
    public function index()
    {
        $query = Obligation::with(['partner', 'partnerService']);

        if ($q = request('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%")
                    ->orWhere('priority', 'like', "%{$q}%");
            })->orWhereHas('partner', function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%");
            })->orWhereHas('partnerService', function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%");
            });
        }

        if ($partnerId = request('partner_id')) {
            $query->where('partner_id', $partnerId);
        }

        if (request('status') === 'open') {
            $query->where('status', 'open');
        }

        if (request('status') === 'done') {
            $query->where(function ($sub) {
                $sub->where('status', 'done')
                    ->orWhereNotNull('completed_date');
            });
        }

        if (request('due') === 'today') {
            $query->whereNull('completed_date')
                ->whereDate('due_date', now()->toDateString());
        }

        if (request('due') === 'soon') {
            $query->whereNull('completed_date')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<=', now()->addDays(7))
                ->whereDate('due_date', '>=', now());
        }

        if (request('due') === 'overdue') {
            $query->whereNull('completed_date')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now());
        }

        $obligations = $query
            ->orderBy('due_date')
            ->get();

        $partners = Partner::orderBy('name')->get();

        return view('obligations.index', compact('obligations', 'partners'));
    }

    public function create()
    {
        return view('obligations.create', [
            'partners' => Partner::orderBy('name')->get(),
            'services' => PartnerService::orderBy('name')->get(),
            'contacts' => PartnerContact::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'partner_service_id' => ['nullable', 'exists:partner_services,id'],
            'partner_contact_id' => ['nullable', 'exists:partner_contacts,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'waiting', 'done'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'due_date' => ['nullable', 'date'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurrence_type' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'remind_days_before' => ['nullable', Rule::in([0, 1, 3, 7, 14, 30])],
        ]);

        if (!empty($data['partner_service_id'])) {
            $serviceBelongsToPartner = PartnerService::where('id', $data['partner_service_id'])
                ->where('partner_id', $data['partner_id'])
                ->exists();

            if (!$serviceBelongsToPartner) {
                return back()
                    ->withErrors([
                        'partner_service_id' => 'Odabrana usluga ne pripada odabranom partneru.',
                    ])
                    ->withInput();
            }
        }

        if (!empty($data['partner_contact_id'])) {
            $contactBelongsToPartner = PartnerContact::where('id', $data['partner_contact_id'])
                ->where('partner_id', $data['partner_id'])
                ->exists();

            if (!$contactBelongsToPartner) {
                return back()
                    ->withErrors([
                        'partner_contact_id' => 'Odabrani kontakt ne pripada odabranom partneru.',
                    ])
                    ->withInput();
            }
        }

        $data['is_recurring'] = $request->boolean('is_recurring');

        if (!$data['is_recurring']) {
            $data['recurrence_type'] = null;
        }

        if ($request->filled('remind_days_before')) {
            $data['remind_days_before'] = (int) $request->input('remind_days_before');
        } else {
            $data['remind_days_before'] = null;
        }

        if ($data['status'] === 'done') {
            $data['completed_date'] = now()->toDateString();
        } else {
            $data['completed_date'] = null;
        }

        Obligation::create($data);

        return redirect()->route('obligations.index');
    }

    public function show(string $id)
    {
        $obligation = Obligation::with(['partner', 'partnerService'])->findOrFail($id);

        return view('obligations.show', compact('obligation'));
    }

    public function edit(string $id)
    {
        $obligation = Obligation::findOrFail($id);

        return view('obligations.edit', [
            'obligation' => $obligation,
            'partners' => Partner::orderBy('name')->get(),
            'services' => PartnerService::orderBy('name')->get(),
            'contacts' => PartnerContact::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'partner_service_id' => ['nullable', 'exists:partner_services,id'],
            'partner_contact_id' => ['nullable', 'exists:partner_contacts,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'waiting', 'done'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'due_date' => ['nullable', 'date'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurrence_type' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'remind_days_before' => ['nullable', Rule::in([0, 1, 3, 7, 14, 30])],
        ]);

        if (!empty($data['partner_service_id'])) {
            $serviceBelongsToPartner = PartnerService::where('id', $data['partner_service_id'])
                ->where('partner_id', $data['partner_id'])
                ->exists();

            if (!$serviceBelongsToPartner) {
                return back()
                    ->withErrors([
                        'partner_service_id' => 'Odabrana usluga ne pripada odabranom partneru.',
                    ])
                    ->withInput();
            }
        }

        if (!empty($data['partner_contact_id'])) {
            $contactBelongsToPartner = PartnerContact::where('id', $data['partner_contact_id'])
                ->where('partner_id', $data['partner_id'])
                ->exists();

            if (!$contactBelongsToPartner) {
                return back()
                    ->withErrors([
                        'partner_contact_id' => 'Odabrani kontakt ne pripada odabranom partneru.',
                    ])
                    ->withInput();
            }
        }

        $data['is_recurring'] = $request->boolean('is_recurring');

        if (!$data['is_recurring']) {
            $data['recurrence_type'] = null;
        }

        if ($request->filled('remind_days_before')) {
            $data['remind_days_before'] = (int) $request->input('remind_days_before');
        } else {
            $data['remind_days_before'] = null;
        }

        if ($data['status'] === 'done') {
            $data['completed_date'] = now()->toDateString();
        } else {
            $data['completed_date'] = null;
        }

        $obligation = Obligation::findOrFail($id);
        $obligation->update($data);

        return redirect()->route('obligations.index');
    }

    public function destroy(string $id)
    {
        Obligation::findOrFail($id)->delete();

        return redirect()->route('obligations.index');
    }

    public function complete(string $id)
    {
        $obligation = Obligation::findOrFail($id);

        $obligation->update([
            'status' => 'done',
            'completed_date' => now(),
        ]);

        return back();
    }
}