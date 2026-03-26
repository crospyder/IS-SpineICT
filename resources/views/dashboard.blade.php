@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Partneri</div>
        <div class="text-3xl font-semibold">{{ $partnersCount }}</div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Usluge</div>
        <div class="text-3xl font-semibold">{{ $servicesCount }}</div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Obveze</div>
        <div class="text-3xl font-semibold">{{ $obligationsCount }}</div>
    </div>

    <div class="app-card p-5">
        <div class="text-sm app-muted mb-2">Ističe uskoro</div>
        <div class="text-3xl font-semibold">{{ $expiringCount }}</div>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

    <div class="app-card p-5 xl:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Kritične obveze</h2>
            <a href="{{ route('obligations.index') }}" class="app-link text-sm">Sve obveze</a>
        </div>

        @if(isset($expiringSoonList) && $expiringSoonList->count())
            <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
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
                        @foreach($expiringSoonList as $obligation)
                            <tr class="app-row">
                                <td>{{ $obligation->partner->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('obligations.edit', $obligation) }}" class="app-link">
                                        {{ $obligation->title }}
                                    </a>
                                </td>
                                <td>{{ $obligation->due_date ? $obligation->due_date->format('Y-m-d') : '-' }}</td>
                                <td>
                                    @if($obligation->isOverdue())
                                        <span class="app-badge badge-overdue">Kasni</span>
                                    @elseif($obligation->isExpiringSoon())
                                        <span class="app-badge badge-soon">Uskoro</span>
                                    @else
                                        <span class="app-badge badge-ok">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="app-muted">Nema kritičnih obveza.</div>
        @endif
    </div>

    <div class="app-card p-5">
        <h2 class="text-lg font-semibold mb-4">Brze akcije</h2>

        <div class="flex flex-col gap-3">
            <a href="{{ route('partners.create') }}" class="app-button">Dodaj partnera</a>
            <a href="{{ route('partner-services.create') }}" class="app-button-secondary">Dodaj uslugu</a>
            <a href="{{ route('obligations.create') }}" class="app-button-secondary">Dodaj obvezu</a>
        </div>
    </div>

</div>

@endsection