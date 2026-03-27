@extends('layouts.app')

@section('title', 'Kalkulacije')

@section('content')

<div class="max-w-7xl">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Kalkulacije</h2>

        <a href="{{ route('procurements.create') }}" class="app-button">
            Nova kalkulacija
        </a>
    </div>

    <div class="app-card p-4">
        <table class="app-table">
            <thead>
                <tr>
                    <th>Naziv</th>
                    <th>Partner</th>
                    <th>Status</th>
                    <th>Datum</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->partner->name ?? '-' }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->created_at->format('d.m.Y') }}</td>
                        <td>
                            <a href="{{ route('procurements.edit', $item) }}" class="app-link">
                                Otvori
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="app-muted">
                            Nema kalkulacija
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection