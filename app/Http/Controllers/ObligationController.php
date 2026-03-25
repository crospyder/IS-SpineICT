<?php

namespace App\Http\Controllers;

use App\Models\Obligation;
use App\Models\Partner;
use App\Models\PartnerService;
use Illuminate\Http\Request;

class ObligationController extends Controller
{
    public function index()
    {
        $query = Obligation::with(['partner', 'partnerService']);

        if (request('status') === 'open') {
            $query->where('status', 'open');
        }

        if (request('due') === 'soon') {
            $query->whereNotNull('due_date')
                  ->whereDate('due_date', '<=', now()->addDays(7));
        }

        if (request('due') === 'overdue') {
            $query->whereNotNull('due_date')
                  ->whereDate('due_date', '<', now());
        }

        $items = $query->orderBy('due_date')->get();

        return view('obligations.index', compact('items'));
    }

    public function create()
    {
        return view('obligations.create', [
            'partners' => Partner::orderBy('name')->get(),
            'services' => PartnerService::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'partner_service_id' => 'nullable|exists:partner_services,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'due_date' => 'nullable|date',
        ]);

        Obligation::create($data);

        return redirect('/obligations');
    }

    public function show(string $id)
    {
        $item = Obligation::with(['partner', 'partnerService'])->findOrFail($id);
        return view('obligations.show', compact('item'));
    }

    public function edit(string $id)
    {
        $item = Obligation::findOrFail($id);

        return view('obligations.edit', [
            'item' => $item,
            'partners' => Partner::orderBy('name')->get(),
            'services' => PartnerService::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'partner_service_id' => 'nullable|exists:partner_services,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'due_date' => 'nullable|date',
        ]);

        $item = Obligation::findOrFail($id);
        $item->update($data);

        return redirect('/obligations/' . $item->id);
    }

    public function destroy(string $id)
    {
        Obligation::findOrFail($id)->delete();
        return redirect('/obligations');
    }
}