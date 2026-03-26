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
        <div class="mt-2 text-sm">
            @if($expiringServicesCount > 0)
                <span class="app-badge badge-soon">Ističe uskoro: {{ $expiringServicesCount }}</span>
            @else
                <span class="app-badge badge-ok">Nema skorih isteka</span>
            @endif
        </div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Obveze</div>
        <div class="text-3xl font-semibold">{{ $obligationsCount }}</div>
        <div class="mt-2 text-sm">
            @if($overdueCount > 0)
                <span class="app-badge badge-overdue">Kasni: {{ $overdueCount }}</span>
            @else
                <span class="app-badge badge-ok">Nema kašnjenja</span>
            @endif
        </div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Uskoro dospijeva</div>
        <div class="text-3xl font-semibold">{{ $expiringCount }}</div>
        <div class="mt-2 text-sm app-muted">
            Obveze u idućih 7 dana
        </div>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

    <div class="app-card p-5 xl:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Obveze koje kasne</h2>
            <a href="{{ route('obligations.index', ['due' => 'overdue']) }}" class="app-link text-sm">Otvori popis</a>
        </div>

        @if($overdueList->count())
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th>Naslov</th>
                        <th>Rok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overdueList as $obligation)
                        <tr class="app-row">
                            <td>{{ $obligation->partner->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('obligations.edit', $obligation) }}" class="app-link">
                                    {{ $obligation->title }}
                                </a>
                            </td>
                            <td>{{ $obligation->due_date ? $obligation->due_date->format('Y-m-d') : '-' }}</td>
                            <td>
                                <span class="app-badge badge-overdue">Kasni</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema obveza u kašnjenju.</div>
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
            <h2 class="text-lg font-semibold">Obveze uskoro</h2>
            <a href="{{ route('obligations.index', ['due' => 'soon']) }}" class="app-link text-sm">Otvori popis</a>
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
                                    {{ $obligation->due_date ? $obligation->due_date->format('Y-m-d') : '-' }}
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
                                <span class="app-badge badge-soon">
                                    {{ $service->expires_on ? \Carbon\Carbon::parse($service->expires_on)->format('Y-m-d') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="app-muted">Nema usluga pred istekom u idućih 30 dana.</div>
        @endif
    </div>

</div>

@endsection