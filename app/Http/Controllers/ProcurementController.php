<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Procurement;
use Illuminate\Http\Request;
use App\Models\ProcurementItem;
use App\Models\ProcurementCost;

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

        $items = $query->paginate(20)->withQueryString();

        return view('procurements.index', [
            'items' => $items,
            'filters' => $request->only(['q', 'status']),
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

    public function storeItem(Request $request, Procurement $procurement)
    {
        $data = $request->validate([
            'sort_order' => 'nullable|integer|min:0',
            'item_type' => 'required|in:goods,service,software,hardware,subscription,other',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0.001',
            'supplier_origin' => 'required|in:domestic,foreign',
            'supplier_name' => 'nullable|string|max:255',
            'purchase_net_unit' => 'required|numeric|min:0',
            'sale_net_unit' => 'required|numeric|min:0',
            'purchase_vat_rate' => 'required|numeric|min:0|max:100',
            'sale_vat_rate' => 'required|numeric|min:0|max:100',
            'is_optional' => 'nullable|boolean',
            'status_flag' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? ((int) $procurement->items()->max('sort_order') + 1);
        $data['is_optional'] = $request->boolean('is_optional');

        $procurement->items()->create($data);

        return redirect()
            ->route('procurements.edit', $procurement)
            ->with('success', 'Stavka je dodana.');
    }

    public function updateItem(Request $request, Procurement $procurement, ProcurementItem $procurementItem)
    {
        abort_unless($procurementItem->procurement_id === $procurement->id, 404);

        $data = $request->validate([
            'sort_order' => 'nullable|integer|min:0',
            'item_type' => 'required|in:goods,service,software,hardware,subscription,other',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0.001',
            'supplier_origin' => 'required|in:domestic,foreign',
            'supplier_name' => 'nullable|string|max:255',
            'purchase_net_unit' => 'required|numeric|min:0',
            'sale_net_unit' => 'required|numeric|min:0',
            'purchase_vat_rate' => 'required|numeric|min:0|max:100',
            'sale_vat_rate' => 'required|numeric|min:0|max:100',
            'is_optional' => 'nullable|boolean',
            'status_flag' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $data['is_optional'] = $request->boolean('is_optional');

        $procurementItem->update($data);

        return redirect()
            ->route('procurements.edit', $procurement)
            ->with('success', 'Stavka je ažurirana.');
    }

    public function destroyItem(Procurement $procurement, ProcurementItem $procurementItem)
    {
        abort_unless($procurementItem->procurement_id === $procurement->id, 404);

        $procurementItem->delete();

        return redirect()
            ->route('procurements.edit', $procurement)
            ->with('success', 'Stavka je obrisana.');
    }

    public function storeCost(Request $request, Procurement $procurement)
    {
        $data = $request->validate([
            'cost_type' => 'required|in:field,logistics,shipping,customs,travel,service,other',
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'nullable|string|max:50',
            'net_amount' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'supplier_origin' => 'required|in:domestic,foreign',
            'include_in_offer' => 'nullable|boolean',
            'include_in_margin' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $data['include_in_offer'] = $request->boolean('include_in_offer');
        $data['include_in_margin'] = $request->boolean('include_in_margin', true);

        $procurement->costs()->create($data);

        return redirect()
            ->route('procurements.edit', $procurement)
            ->with('success', 'Trošak je dodan.');
    }

    public function updateCost(Request $request, Procurement $procurement, ProcurementCost $procurementCost)
    {
        abort_unless($procurementCost->procurement_id === $procurement->id, 404);

        $data = $request->validate([
            'cost_type' => 'required|in:field,logistics,shipping,customs,travel,service,other',
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'nullable|string|max:50',
            'net_amount' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'supplier_origin' => 'required|in:domestic,foreign',
            'include_in_offer' => 'nullable|boolean',
            'include_in_margin' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $data['include_in_offer'] = $request->boolean('include_in_offer');
        $data['include_in_margin'] = $request->boolean('include_in_margin');

        $procurementCost->update($data);

        return redirect()
            ->route('procurements.edit', $procurement)
            ->with('success', 'Trošak je ažuriran.');
    }

    public function destroyCost(Procurement $procurement, ProcurementCost $procurementCost)
    {
        abort_unless($procurementCost->procurement_id === $procurement->id, 404);

        $procurementCost->delete();

        return redirect()
            ->route('procurements.edit', $procurement)
            ->with('success', 'Trošak je obrisan.');
    }
}