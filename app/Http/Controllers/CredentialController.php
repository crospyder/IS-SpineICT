<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Partner;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function create()
    {
        return view('credentials.create', [
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'credential_type' => 'required|string|max:100',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Credential::create($data);

        return redirect()->route('partners.show', $data['partner_id']);
    }

    public function edit(string $id)
    {
        $item = Credential::findOrFail($id);

        return view('credentials.edit', [
            'item' => $item,
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'credential_type' => 'required|string|max:100',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $item = Credential::findOrFail($id);

        if (!$data['password']) {
            unset($data['password']);
        }

        $item->update($data);

        return redirect()->route('partners.show', $data['partner_id']);
    }

    public function destroy(string $id)
    {
        $item = Credential::findOrFail($id);
        $partnerId = $item->partner_id;

        $item->delete();

        return redirect()->route('partners.show', $partnerId);
    }
}