@extends('layouts.app')

@section('title', 'Inventar')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">Inventar</h2>
            <div class="text-sm app-muted mt-1">
                Pregled inventara uređaja po partnerima
            </div>
        </div>

        <a href="{{ route('inventory.create') }}" class="app-button">
            Dodaj uređaj
        </a>
    </div>

    <div class="app-card p-4 mb-6">
        <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="app-form-group md:col-span-2">
                <label class="app-label" for="search">Pretraga</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="app-input"
                    value="{{ request('search') }}"
                    placeholder="Naziv uređaja, serijski broj, model, partner..."
                >
            </div>

            <div class="app-form-group">
                <label class="app-label" for="partner_id">Partner</label>
                <select name="partner_id" id="partner_id" class="app-input">
                    <option value="">Svi partneri</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ (string) request('partner_id') === (string) $partner->id ? 'selected' : '' }}>
                            {{ $partner->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="app-button">Filtriraj</button>
                <a href="{{ route('inventory.index') }}" class="app-button-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="app-card p-6">
        @if($items->count())
            <div class="overflow-hidden">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Partner</th>
                            <th>Naziv uređaja</th>
                            <th>Proizvođač / model</th>
                            <th>Serijski broj</th>
                            <th>Identifikator uređaja</th>
                            <th>Ažurirano</th>
                            <th class="text-right">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="app-row">
                                <td>{{ $item->partner_name ?? '—' }}</td>
                                <td>{{ $item->hostname ?? $item->device_name ?? '—' }}</td>
                                <td>{{ trim(($item->manufacturer ?? '') . ' ' . ($item->model ?? '')) ?: '—' }}</td>
                                <td>{{ $item->serial_number ?? '—' }}</td>
                                <td class="break-all">{{ $item->agent_device_id ?? '—' }}</td>
                                <td>{{ $item->updated_at ?? '—' }}</td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('inventory.show', $item->id) }}" class="app-button-secondary">
                                            Otvori
                                        </a>
                                        <a href="{{ route('inventory.edit', $item->id) }}" class="app-button-secondary">
                                            Uredi
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $items->links() }}
            </div>
        @else
            <div class="app-muted">Nema inventurnih zapisa.</div>
        @endif
    </div>
</div>

@endsection