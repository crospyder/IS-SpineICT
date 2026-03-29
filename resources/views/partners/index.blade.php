@extends('layouts.app')

@section('title', 'Partneri')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Partneri</h2>

    <a href="{{ route('partners.create') }}" class="app-button">
        Dodaj partnera
    </a>
</div>

<form method="GET" action="{{ route('partners.index') }}" class="app-card p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label class="app-label" for="q">Pretraga</label>
            <input type="text" id="q" name="q" class="app-input" value="{{ request('q') }}" placeholder="Naziv, OIB, email, telefon, grad...">
        </div>

        <div>
            <label class="app-label" for="active">Status</label>
            <select id="active" name="active" class="app-select">
                <option value="">Svi</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktivni</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Neaktivni</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="app-button">Filtriraj</button>
            <a href="{{ route('partners.index') }}" class="app-button-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="app-card overflow-hidden">
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Naziv</th>
                <th>Pravni naziv</th>
                <th>OIB</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Grad</th>
                <th>Status</th>
                <th class="text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
        @forelse($partners as $partner)
            <tr class="app-row">
                <td>{{ $partner->id }}</td>

                <td>
    <a href="{{ route('partners.show', $partner) }}" class="app-link">
        {{ $partner->name }}
    </a>
</td>

                <td>{{ $partner->legal_name ?: '-' }}</td>
                <td>{{ $partner->oib ?: '-' }}</td>
                <td>{{ $partner->email ?: '-' }}</td>
                <td>{{ $partner->phone ?: '-' }}</td>
                <td>{{ $partner->city ?: '-' }}</td>

                <td>
                    @if($partner->is_active)
                        <span class="app-badge badge-ok">Aktivan</span>
                    @else
                        <span class="app-badge badge-overdue">Neaktivan</span>
                    @endif
                </td>

                <td class="text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('partners.edit', $partner) }}" class="app-button-secondary">
                            Uredi
                        </a>

                        <form action="{{ route('partners.destroy', $partner) }}"
                              method="POST"
                              onsubmit="return confirm('Obrisati partnera?');">
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
                    Nema partnera
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection