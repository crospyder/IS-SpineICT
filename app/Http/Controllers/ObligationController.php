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

        return view('obligations.index', compact('obligations'));
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

        $obligation = Obligation::findOrFail($id);
        $obligation->update($data);

        return redirect()->route('obligations.index');
    }

    public function destroy(string $id)
    {
        Obligation::findOrFail($id)->delete();

        return redirect()->route('obligations.index');
    }
}