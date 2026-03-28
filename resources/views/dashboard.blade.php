@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-6">

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

<div class="grid grid-cols-1 2xl:grid-cols-12 gap-6 mt-6">

    <div class="2xl:col-span-9 space-y-6 min-w-0">

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="app-card p-5 xl:col-span-8 min-w-0">
                <div class="flex justify-between items-center gap-4 mb-4">
                    <h2 class="text-lg font-semibold">Kasni / isteklo</h2>
                    <div class="text-sm app-muted text-right">Problemi koji traže pažnju</div>
                </div>

                @if($alertsList->count())
                    <div class="overflow-x-auto">
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
                    </div>
                @else
                    <div class="app-muted">Nema kašnjenja ni isteklih usluga.</div>
                @endif
            </div>

            <div class="app-card p-5 xl:col-span-4">
                <h2 class="text-lg font-semibold mb-4">Brze akcije</h2>

                <div class="flex flex-col gap-2">
                    <a href="{{ route('partners.create') }}" class="app-button text-center text-sm py-2">Dodaj partnera</a>
                    <a href="{{ route('partner-services.create') }}" class="app-button-secondary text-center text-sm py-2">Dodaj uslugu</a>
                    <a href="{{ route('obligations.create') }}" class="app-button-secondary text-center text-sm py-2">Dodaj obvezu</a>
                    <a href="{{ route('obligations.index') }}" class="app-button-secondary text-center text-sm py-2">Otvori obveze</a>
                </div>
            </div>
        </div>

        <div class="app-card p-5 min-w-0">
            <div class="flex justify-between items-center gap-4 mb-4">
                <h2 class="text-lg font-semibold">Slijedi po redu</h2>
                <div class="text-sm app-muted text-right">Sljedeće obveze i isteci</div>
            </div>

            @if($nextItems->count())
                <div class="overflow-x-auto">
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
                </div>
            @else
                <div class="app-muted">Nema nadolazećih obveza ni usluga s datumom.</div>
            @endif
        </div>

    </div>

    <div class="2xl:col-span-3 min-w-0">
        <div class="app-card p-5 h-full">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Zadnje aktivnosti</h2>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2 mb-5">
                <select name="activity_entity" class="app-input py-1.5 px-2 text-xs min-w-[120px]">
                    <option value="">Sve</option>
                    @foreach($activityAvailableEntities as $entityValue => $entityLabel)
                        <option value="{{ $entityValue }}" @selected($activityEntity === $entityValue)>
                            {{ $entityLabel }}
                        </option>
                    @endforeach
                </select>

                <label class="inline-flex items-center gap-2 text-xs app-muted whitespace-nowrap">
                    <input type="checkbox" name="activity_mine" value="1" @checked($activityMine)>
                    Moje
                </label>

                <select name="activity_limit" class="app-input py-1.5 px-2 text-xs w-20">
                    <option value="10" @selected($activityLimit === 10)>10</option>
                    <option value="25" @selected($activityLimit === 25)>25</option>
                    <option value="50" @selected($activityLimit === 50)>50</option>
                </select>

                <button type="submit" class="app-button-secondary py-1.5 px-2 text-xs">
                    Primijeni
                </button>

                @if($activityEntity || $activityMine || $activityLimit !== 10)
                    <a href="{{ route('dashboard') }}" class="app-link text-xs whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </form>

            @if($recentActivities->count())
                <div class="space-y-4 text-sm">
                    @foreach($recentActivities as $activity)
                        <div class="border-b border-white/10 pb-4 last:border-b-0 last:pb-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium break-words leading-tight">
                                        {{ $activity->title ?: $activity->entity_label }}
                                    </div>

                                    @if(!empty($activity->message))
                                        <div class="text-sm mt-1 app-muted break-words leading-snug">
                                            {{ $activity->message }}
                                        </div>
                                    @endif

                                    <div class="text-xs app-muted mt-2">
                                        {{ $activity->user->name ?? 'Sustav' }}
                                    </div>

                                    @if($activity->has_changes)
                                        <details class="mt-3">
                                            <summary class="cursor-pointer text-xs app-muted select-none">
                                                Prikaži promjene
                                            </summary>

                                            <div class="mt-3 overflow-x-auto">
                                                <table class="app-table text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Polje</th>
                                                            <th>Prije</th>
                                                            <th>Poslije</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($activity->change_rows as $change)
                                                            <tr class="app-row">
                                                                <td>{{ $change['label'] }}</td>
                                                                <td>{{ $change['old'] }}</td>
                                                                <td>{{ $change['new'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </details>
                                    @endif
                                </div>

                                <div class="text-xs app-muted whitespace-nowrap shrink-0">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="app-muted">Nema aktivnosti za odabrani filter.</div>
            @endif
        </div>
    </div>

</div>

@endsection