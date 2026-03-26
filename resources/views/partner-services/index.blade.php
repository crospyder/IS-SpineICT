@extends('layouts.app')

@section('title', 'Usluge')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Usluge</h2>

    <div class="flex items-center gap-3">
        <a href="{{ route('partner-services.index', ['expiring' => 1]) }}" class="app-button-secondary">
            Ističe 30 dana
        </a>

        <a href="{{ route('partner-services.create') }}" class="app-button">
            Dodaj uslugu
        </a>
    </div>
</div>

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