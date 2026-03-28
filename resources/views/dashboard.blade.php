@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Partneri</div>
        <div class="text-3xl font-semibold">{{ $partnersCount }}</div>
        <div class="mt-2 text-sm app-muted">
            Aktivni: {{ $activePartnersCount }}
        </div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Usluge</div>
        <div class="text-3xl font-semibold">{{ $servicesCount }}</div>
        <div class="mt-2 text-sm app-muted">
            Evidentirane usluge u sustavu
        </div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Aktivne obveze</div>
        <div class="text-3xl font-semibold">{{ $activeObligationsCount }}</div>
        <div class="mt-2 text-sm app-muted">
            Obveze bez completed datuma
        </div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Upozorenja</div>
        <div class="text-3xl font-semibold">{{ $alertsCount }}</div>
        <div class="mt-2 text-sm flex flex-wrap gap-2">
            @if($alertsCount > 0)
                <span class="app-badge badge-overdue">Kasni / isteklo: {{ $alertsCount }}</span>
            @else
                <span class="app-badge badge-ok">Nema upozorenja</span>
            @endif
        </div>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

    <div class="app-card p-5 xl:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Kasni / isteklo</h2>
            <div class="text-sm app-muted">Problemi koji traže pažnju</div>
        </div>

        @if($alertsList->count())
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Tip</th>
                        <th>Partner</th>
                        <th>Naslov</th>
                        <th>Datum</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alertsList as $entry)
                        <tr class="app-row">
                            <td>{{ $entry->kind_label }}</td>
                            <td>{{ $entry->partner_name }}</td>
                            <td>
                                <a href="{{ $entry->url }}" class="app-link">
                                    {{ $entry->title }}
                                </a>
                            </td>
                            <td>{{ $entry->date ? $entry->date->format('d.m.Y') : '-' }}</td>
                            <td>
                                <span class="app-badge {{ $entry->status_class }}">
                                    {{ $entry->status_label }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema kašnjenja ni isteklih usluga.</div>
        @endif
    </div>

    <div class="app-card p-5">
        <h2 class="text-lg font-semibold mb-4">Brze akcije</h2>

        <div class="flex flex-col gap-3">
            <a href="{{ route('partners.create') }}" class="app-button">Dodaj partnera</a>
            <a href="{{ route('partner-services.create') }}" class="app-button-secondary">Dodaj uslugu</a>
            <a href="{{ route('obligations.create') }}" class="app-button-secondary">Dodaj obvezu</a>
            <a href="{{ route('obligations.index') }}" class="app-button-secondary">Otvori obveze</a>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">

    <div class="app-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Slijedi po redu</h2>
            <div class="text-sm app-muted">Sljedeće obveze i isteci</div>
        </div>

        @if($nextItems->count())
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Tip</th>
                        <th>Partner</th>
                        <th>Naslov</th>
                        <th>Datum</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nextItems as $entry)
                        <tr class="app-row">
                            <td>{{ $entry->kind_label }}</td>
                            <td>{{ $entry->partner_name }}</td>
                            <td>
                                <a href="{{ $entry->url }}" class="app-link">
                                    {{ $entry->title }}
                                </a>
                            </td>
                            <td>{{ $entry->date ? $entry->date->format('d.m.Y') : '-' }}</td>
                            <td>
                                <span class="app-badge {{ $entry->status_class }}">
                                    {{ $entry->status_label }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema nadolazećih obveza ni usluga s datumom.</div>
        @endif
    </div>

    <div class="app-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Zadnje aktivnosti</h2>
        </div>

        @if($recentActivities->count())
            <div class="space-y-3">
                @foreach($recentActivities as $activity)
                    <div class="border-b border-white/10 pb-3 last:border-b-0 last:pb-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-medium">
                                    {{ $activity->title ?: $activity->entity_label }}
                                </div>

                                @if(!empty($activity->message))
                                    <div class="text-sm mt-1 app-muted break-words">
                                        {{ $activity->message }}
                                    </div>
                                @endif

                                <div class="text-xs app-muted mt-2">
                                    {{ $activity->user->name ?? 'Sustav' }}
                                </div>
                            </div>

                            <div class="text-xs app-muted whitespace-nowrap shrink-0">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="app-muted">Još nema aktivnosti.</div>
        @endif
    </div>

</div>

@endsection