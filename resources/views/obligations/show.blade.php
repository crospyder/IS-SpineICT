@extends('layouts.app')

@section('title', 'Detalji obveze')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Detalji obveze</h2>

        <div class="flex gap-2">
            <a href="{{ route('obligations.edit', $obligation) }}" class="app-button">
                Uredi
            </a>

            <a href="{{ route('obligations.index') }}" class="app-button-secondary">
                Natrag
            </a>
        </div>
    </div>

    <div class="app-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <div class="app-muted text-sm mb-1">Naslov</div>
                <div class="font-medium">{{ $obligation->title ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Partner</div>
                <div>{{ $obligation->partner?->name ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Usluga</div>
                <div>{{ $obligation->partnerService?->name ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Prioritet</div>
                <div>{{ $obligation->priority ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Status</div>
                <div>
                    @if($obligation->isCompleted())
                        <span class="app-badge badge-ok">Završeno</span>
                    @elseif($obligation->isOverdue())
                        <span class="app-badge badge-overdue">Kasni</span>
                    @elseif($obligation->isExpiringSoon())
                        <span class="app-badge badge-soon">Uskoro</span>
                    @else
                        <span class="app-badge badge-ok">Aktivno</span>
                    @endif
                </div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Rok</div>
                <div>
                    @if($obligation->due_date)
                        @if($obligation->isOverdue())
                            <span class="app-badge badge-overdue">{{ $obligation->due_date->format('Y-m-d') }}</span>
                        @elseif($obligation->isExpiringSoon())
                            <span class="app-badge badge-soon">{{ $obligation->due_date->format('Y-m-d') }}</span>
                        @else
                            <span class="app-badge badge-ok">{{ $obligation->due_date->format('Y-m-d') }}</span>
                        @endif
                    @else
                        -
                    @endif
                </div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Završeno</div>
                <div>{{ $obligation->completed_date ? $obligation->completed_date->format('Y-m-d H:i') : '-' }}</div>
            </div>

            <div class="md:col-span-2">
                <div class="app-muted text-sm mb-1">Opis</div>
                <div>{{ $obligation->description ?: '-' }}</div>
            </div>

        </div>
    </div>
</div>

@endsection