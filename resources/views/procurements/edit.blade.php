@extends('layouts.app')

@section('title', 'Uredi kalkulaciju')

@section('content')

<div class="max-w-7xl">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">{{ $item->title }}</h2>

        <a href="{{ route('procurements.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <div class="app-muted text-sm">Partner</div>
                <div>{{ $item->partner->name ?? '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm">Status</div>
                <div>{{ $item->status }}</div>
            </div>

            <div>
                <div class="app-muted text-sm">Valuta</div>
                <div>{{ $item->default_sale_currency }}</div>
            </div>

        </div>
    </div>

    <div class="app-card p-6 mt-6">
        <div class="app-muted">
            (sljedeći korak: stavke + troškovi + kalkulacija)
        </div>
    </div>

</div>

@endsection