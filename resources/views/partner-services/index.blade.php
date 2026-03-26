@extends('layouts.app')

@section('title', 'Usluge')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Usluge</h2>

    <a href="{{ route('partner-services.create') }}" class="app-button">
        Dodaj uslugu
    </a>
</div>

<form method="GET" action="{{ route('partner-services.index') }}" class="app-card p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <label class="app-label" for="q">Pretraga</label>
            <input type="text" id="q" name="q" class="app-input" value="{{ request('q') }}" placeholder="Naziv, partner, domena, provider...">
        </div>

        <div>
            <label class="app-label" for="partner_id">Partner</label>
            <select id="partner_id" name="partner_id" class="app-select">
                <option value="">Svi</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ (string) request('partner_id') === (string) $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="app-label" for="active">Status</label>
            <select id="active" name="active" class="app-select">
                <option value="">Svi</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktivne</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Neaktivne</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="app-button">Filtriraj</button>
            <a href="{{ route('partner-services.index') }}" class="app-button-secondary">Reset</a>
        </div>
    </div>

    <div class="mt-3">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="expiring" value="1" {{ request('expiring') ? 'checked' : '' }}>
            <span>Prikaži samo usluge koje ističu unutar 30 dana</span>
        </label>
    </div>
</form>

<div class="app-card overflow-hidden">
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Partner</th>
                <th>Naziv</th>
                <th>Tip</th>
                <th>Domena</th>
                <th>Provider</th>
                <th>Status</th>
                <th>Istek</th>
                <th class="text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
        @forelse($partnerServices as $service)
            <tr class="app-row">
                <td>{{ $service->id }}</td>

                <td>{{ $service->partner->name ?? '-' }}</td>

                <td>
                    <a href="{{ route('partner-services.edit', $service) }}" class="app-link">
                        {{ $service->name }}
                    </a>
                </td>

                <td>{{ $service->service_type ?: '-' }}</td>
                <td>{{ $service->domain_name ?: '-' }}</td>
                <td>{{ $service->provider ?: '-' }}</td>
                <td>{{ $service->status ?: '-' }}</td>

                <td>
                    @if($service->expires_on)
                        @php
                            $expires = \Carbon\Carbon::parse($service->expires_on);
                        @endphp

                        @if($expires->isPast())
                            <span class="app-badge badge-overdue">{{ $expires->format('Y-m-d') }}</span>
                        @elseif($expires->lte(now()->addDays(30)))
                            <span class="app-badge badge-soon">{{ $expires->format('Y-m-d') }}</span>
                        @else
                            <span class="app-badge badge-ok">{{ $expires->format('Y-m-d') }}</span>
                        @endif
                    @else
                        -
                    @endif
                </td>

                <td class="text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('partner-services.edit', $service) }}" class="app-button-secondary">
                            Uredi
                        </a>

                        <form action="{{ route('partner-services.destroy', $service) }}"
                              method="POST"
                              onsubmit="return confirm('Obrisati uslugu?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="app-button-secondary">
                                Obriši
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-6 app-muted">
                    Nema usluga
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection