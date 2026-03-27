<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\Partner;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index()
    {
        $items = Procurement::with('partner')
            ->orderByDesc('created_at')
            ->get();

        return view('procurements.index', compact('items'));
    }

    public function create()
    {
        return view('procurements.create', [
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'default_sale_currency' => 'required|string',
            'default_purchase_currency' => 'required|string',
            'fx_eur_to_usd' => 'required|numeric',
            'fx_usd_to_eur' => 'required|numeric',
            'vat_rate' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = 'draft';

        $procurement = Procurement::create($data);

        return redirect()->route('procurements.edit', $procurement);
    }

    public function show(string $id)
    {
        $item = Procurement::with(['partner', 'items', 'costs'])->findOrFail($id);

        return view('procurements.show', compact('item'));
    }

    public function edit(string $id)
    {
        $item = Procurement::with(['items', 'costs'])->findOrFail($id);

        return view('procurements.edit', [
            'item' => $item,
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $item = Procurement::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|string',
        ]);

        $item->update($data);

        return back();
    }

    public function destroy(string $id)
    {
        $item = Procurement::findOrFail($id);
        $item->delete();

        return redirect()->route('procurements.index');
    }
}