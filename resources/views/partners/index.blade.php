@extends('layouts.app')

@section('title', 'Partneri')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Partneri</h2>

    <a href="{{ route('partners.create') }}" class="app-button">
        Dodaj partnera
    </a>
</div>

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
                    <a href="{{ route('partners.edit', $partner) }}" class="app-link">
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