<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Procurement;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $query = Procurement::with('partner')
            ->orderByDesc('created_at');

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $query->where(function ($sub) use ($search) {
                $sub->where('title', 'like', '%' . $search . '%')
                    ->orWhere('reference_no', 'like', '%' . $search . '%')
                    ->orWhereHas('partner', function ($partnerQuery) use ($search) {
                        $partnerQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sale_currency')) {
            $query->where('default_sale_currency', $request->sale_currency);
        }

        if ($request->filled('purchase_currency')) {
            $query->where('default_purchase_currency', $request->purchase_currency);
        }

        $items = $query->paginate(20)->withQueryString();

        return view('procurements.index', [
            'items' => $items,
            'filters' => $request->only(['q', 'status', 'sale_currency', 'purchase_currency']),
            'statuses' => [
                'draft' => 'Draft',
                'in_review' => 'U obradi',
                'ready' => 'Spremno',
                'sent' => 'Poslano',
                'approved' => 'Odobreno',
                'rejected' => 'Odbijeno',
                'archived' => 'Arhivirano',
            ],
            'currencies' => ['EUR', 'USD'],
        ]);
    }

    public function create()
    {
        return view('procurements.create', [
            'partners' => Partner::orderBy('name')->get(),
            'statuses' => [
                'draft' => 'Draft',
                'in_review' => 'U obradi',
                'ready' => 'Spremno',
                'sent' => 'Poslano',
                'approved' => 'Odobreno',
                'rejected' => 'Odbijeno',
                'archived' => 'Arhivirano',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'offer_date' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:offer_date',
            'default_sale_currency' => 'required|in:EUR,USD',
            'default_purchase_currency' => 'required|in:EUR,USD',
            'fx_eur_to_usd' => 'required|numeric|min:0.000001',
            'fx_usd_to_eur' => 'required|numeric|min:0.000001',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'draft';

        $procurement = Procurement::create($data);

        return redirect()
            ->route('procurements.edit', $procurement)
            ->with('success', 'Kalkulacija je kreirana.');
    }

    public function show(string $id)
    {
        return redirect()->route('procurements.edit', $id);
    }

    public function edit(string $id)
    {
        $item = Procurement::with(['partner', 'items', 'costs'])->findOrFail($id);

        return view('procurements.edit', [
            'item' => $item,
            'partners' => Partner::orderBy('name')->get(),
            'statuses' => [
                'draft' => 'Draft',
                'in_review' => 'U obradi',
                'ready' => 'Spremno',
                'sent' => 'Poslano',
                'approved' => 'Odobreno',
                'rejected' => 'Odbijeno',
                'archived' => 'Arhivirano',
            ],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $item = Procurement::findOrFail($id);

        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'offer_date' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:offer_date',
            'default_sale_currency' => 'required|in:EUR,USD',
            'default_purchase_currency' => 'required|in:EUR,USD',
            'fx_eur_to_usd' => 'required|numeric|min:0.000001',
            'fx_usd_to_eur' => 'required|numeric|min:0.000001',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $item->update($data);

        return redirect()
            ->route('procurements.edit', $item)
            ->with('success', 'Kalkulacija je ažurirana.');
    }

    public function destroy(string $id)
    {
        $item = Procurement::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('procurements.index')
            ->with('success', 'Kalkulacija je obrisana.');
    }
}