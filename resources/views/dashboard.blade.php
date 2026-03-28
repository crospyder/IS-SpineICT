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
        <div class="mt-2 text-sm flex flex-wrap gap-2">
            @if($expiringServicesCount > 0)
                <span class="app-badge badge-soon">Ističe / isteklo u 30 dana: {{ $expiringServicesCount }}</span>
            @else
                <span class="app-badge badge-ok">Nema skorih isteka</span>
            @endif
        </div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Obveze i upozorenja</div>
        <div class="text-3xl font-semibold">{{ $obligationsCount }}</div>
        <div class="mt-2 text-sm flex flex-wrap gap-2">
            @if($overdueCount > 0)
                <span class="app-badge badge-overdue">Kasni / isteklo: {{ $overdueCount }}</span>
            @else
                <span class="app-badge badge-ok">Nema kašnjenja</span>
            @endif

            @if($todayCount > 0)
                <span class="app-badge badge-soon">Danas: {{ $todayCount }}</span>
            @endif

            @if($expiringCount > 0)
                <span class="app-badge badge-soon">Uskoro: {{ $expiringCount }}</span>
            @endif
        </div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Uskoro dospijeva</div>
        <div class="text-3xl font-semibold">{{ $expiringCount }}</div>
        <div class="mt-2 text-sm app-muted">
            Obveze i usluge u idućih 30 dana
        </div>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

    <div class="app-card p-5 xl:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Obveze koje kasne</h2>
            <a href="{{ route('obligations.index') }}" class="app-link text-sm">Otvori popis</a>
        </div>

        @if($overdueList->count())
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th>Naslov</th>
                        <th>Rok / istek</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overdueList as $entry)
                        <tr class="app-row">
                            <td>{{ $entry->partner_name }}</td>
                            <td>
                                <a href="{{ $entry->url }}" class="app-link">
                                    {{ $entry->title }}
                                </a>
                            </td>
                            <td>{{ $entry->date ? \Carbon\Carbon::parse($entry->date)->format('d.m.Y') : '-' }}</td>
                            <td>
                                <span class="app-badge badge-overdue">{{ $entry->status_label }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema obveza ni usluga u kašnjenju.</div>
        @endif
    </div>

    <div class="app-card p-5">
        <h2 class="text-lg font-semibold mb-4">Brze akcije</h2>

        <div class="flex flex-col gap-3">
            <a href="{{ route('partners.create') }}" class="app-button">Dodaj partnera</a>
            <a href="{{ route('partner-services.create') }}" class="app-button-secondary">Dodaj uslugu</a>
            <a href="{{ route('obligations.create') }}" class="app-button-secondary">Dodaj obvezu</a>
            <a href="{{ route('partner-services.index', ['expiring' => 1]) }}" class="app-button-secondary">Usluge pred istekom</a>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">

    <div class="app-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Obveze danas</h2>
            <a href="{{ route('obligations.index') }}" class="app-link text-sm">Otvori popis</a>
        </div>

        @if($todayList->count())
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th>Naslov</th>
                        <th>Rok / istek</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayList as $entry)
                        <tr class="app-row">
                            <td>{{ $entry->partner_name }}</td>
                            <td>
                                <a href="{{ $entry->url }}" class="app-link">
                                    {{ $entry->title }}
                                </a>
                            </td>
                            <td>
                                <span class="app-badge badge-soon">
                                    {{ $entry->date ? \Carbon\Carbon::parse($entry->date)->format('d.m.Y') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema obveza ni usluga koje dospijevaju danas.</div>
        @endif
    </div>

    <div class="app-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Obveze uskoro</h2>
            <a href="{{ route('obligations.index') }}" class="app-link text-sm">Otvori popis</a>
        </div>

        @if($expiringSoonList->count())
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th>Naslov</th>
                        <th>Rok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringSoonList as $obligation)
                        <tr class="app-row">
                            <td>{{ $obligation->partner->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('obligations.edit', $obligation) }}" class="app-link">
                                    {{ $obligation->title }}
                                </a>
                            </td>
                            <td>
                                <span class="app-badge badge-soon">
                                    {{ $obligation->due_date ? $obligation->due_date->format('d.m.Y') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema obveza koje uskoro dospijevaju.</div>
        @endif
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">

    <div class="app-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Usluge pred istekom</h2>
            <a href="{{ route('partner-services.index', ['expiring' => 1]) }}" class="app-link text-sm">Otvori popis</a>
        </div>

        @if($expiringServicesList->count())
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th>Usluga</th>
                        <th>Istek</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringServicesList as $service)
                        <tr class="app-row">
                            <td>{{ $service->partner->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('partner-services.edit', $service) }}" class="app-link">
                                    {{ $service->name }}
                                </a>
                            </td>
                            <td>
                                @php
                                    $isExpired = $service->expires_on && \Carbon\Carbon::parse($service->expires_on)->isBefore(now()->startOfDay());
                                @endphp

                                <span class="app-badge {{ $isExpired ? 'badge-overdue' : 'badge-soon' }}">
                                    {{ $service->expires_on ? \Carbon\Carbon::parse($service->expires_on)->format('d.m.Y') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema usluga s istekom u prethodnih ili idućih 30 dana.</div>
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
                            <div>
                                <div class="text-sm font-medium">
                                    {{ $activity->entity_label }}
                                </div>
                                <div class="text-sm mt-1">
                                    {{ $activity->message }}
                                </div>
                                <div class="text-xs app-muted mt-1">
                                    {{ $activity->user->name ?? 'Sustav' }}
                                </div>
                            </div>

                            <div class="text-xs app-muted whitespace-nowrap">
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