@extends('layouts.app')

@section('title', 'Obveze')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Obveze</h2>

    <a href="{{ route('obligations.create') }}" class="app-button">
        Dodaj obvezu
    </a>
</div>

<div class="mb-4 flex gap-3 text-sm">
    <a href="{{ route('obligations.index') }}" class="app-link">Sve</a>
    <a href="{{ route('obligations.index', ['status' => 'open']) }}" class="app-link">Otvoreno</a>
    <a href="{{ route('obligations.index', ['due' => 'soon']) }}" class="app-link">Uskoro</a>
    <a href="{{ route('obligations.index', ['due' => 'overdue']) }}" class="app-link">Kasni</a>
</div>

<div class="app-card overflow-hidden">
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Partner</th>
                <th>Usluga</th>
                <th>Naslov</th>
                <th>Status</th>
                <th>Prioritet</th>
                <th>Rok</th>
                <th class="text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
        @forelse($obligations as $o)
            <tr class="app-row">

                <td>{{ $o->id }}</td>

                <td>{{ $o->partner->name ?? '-' }}</td>

                <td>{{ $o->partnerService->name ?? '-' }}</td>

                <td>
                    <a href="{{ route('obligations.edit', $o) }}" class="app-link">
                        {{ $o->title }}
                    </a>
                </td>

                <td>{{ $o->status }}</td>

                <td>{{ $o->priority }}</td>

                <td>
                    @if($o->due_date)
                        <span class="app-badge
                            @if($o->isOverdue()) badge-overdue
                            @elseif($o->isExpiringSoon()) badge-soon
                            @else badge-ok
                            @endif">
                            {{ $o->due_date->format('Y-m-d') }}
                        </span>
                    @else
                        -
                    @endif
                </td>

                <td class="text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('obligations.edit', $o) }}" class="app-button-secondary">
                            Uredi
                        </a>

                        <form action="{{ route('obligations.destroy', $o) }}"
                              method="POST"
                              onsubmit="return confirm('Obrisati obvezu?');">
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
                <td colspan="8" class="text-center py-6 app-muted">
                    Nema obveza
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection