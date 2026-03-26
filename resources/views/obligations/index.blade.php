@extends('layouts.app')

@section('title', 'Obveze')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Obveze</h2>

    <a href="{{ route('obligations.create') }}" class="app-button">
        Dodaj obvezu
    </a>
</div>

<form method="GET" action="{{ route('obligations.index') }}" class="app-card p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <label class="app-label" for="q">Pretraga</label>
            <input type="text" id="q" name="q" class="app-input" value="{{ request('q') }}" placeholder="Naslov, partner, usluga, status...">
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
            <label class="app-label" for="status">Status</label>
            <select id="status" name="status" class="app-select">
                <option value="">Svi</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Otvoreno</option>
                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Završeno</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="app-button">Filtriraj</button>
            <a href="{{ route('obligations.index') }}" class="app-button-secondary">Reset</a>
        </div>
    </div>

    <div class="mt-3 flex gap-4 text-sm">
        <label class="inline-flex items-center gap-2">
            <input type="radio" name="due" value="" {{ request('due', '') === '' ? 'checked' : '' }}>
            <span>Svi rokovi</span>
        </label>

        <label class="inline-flex items-center gap-2">
            <input type="radio" name="due" value="soon" {{ request('due') === 'soon' ? 'checked' : '' }}>
            <span>Uskoro</span>
        </label>

        <label class="inline-flex items-center gap-2">
            <input type="radio" name="due" value="overdue" {{ request('due') === 'overdue' ? 'checked' : '' }}>
            <span>Kasni</span>
        </label>
    </div>
</form>

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

                <td>
                    @if($o->isCompleted())
                        <span class="app-badge badge-ok">Završeno</span>
                    @elseif($o->isOverdue())
                        <span class="app-badge badge-overdue">Kasni</span>
                    @elseif($o->isExpiringSoon())
                        <span class="app-badge badge-soon">Uskoro</span>
                    @else
                        <span class="app-badge badge-ok">Aktivno</span>
                    @endif
                </td>

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
                        @if(!$o->isCompleted())
                            <form action="{{ route('obligations.complete', $o) }}"
                                  method="POST"
                                  onsubmit="return confirm('Označiti obvezu kao završenu?');">
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="app-button-secondary">
                                    Završi
                                </button>
                            </form>
                        @endif

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