@extends('layouts.app')

@section('title', 'Kalkulacije')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">Kalkulacije</h2>
            <div class="text-sm app-muted mt-1">
                Procurement / ponude / interne kalkulacije
            </div>
        </div>

        <a href="{{ route('procurements.create') }}" class="app-button">
            Nova kalkulacija
        </a>
    </div>

    @if(session('success'))
        <div class="app-card p-4 mb-4">
            <div class="text-sm">{{ session('success') }}</div>
        </div>
    @endif

    <div class="app-card p-4 mb-6">
        <form method="GET" action="{{ route('procurements.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Pretraga</label>
                    <input
                        type="text"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        class="app-input"
                        placeholder="naziv, partner, referenca..."
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Status</label>
                    <select name="status" class="app-select">
                        <option value="">-- svi --</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Prodajna valuta</label>
                    <select name="sale_currency" class="app-select">
                        <option value="">-- sve --</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency }}" @selected(($filters['sale_currency'] ?? '') === $currency)>
                                {{ $currency }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Nabavna valuta</label>
                    <select name="purchase_currency" class="app-select">
                        <option value="">-- sve --</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency }}" @selected(($filters['purchase_currency'] ?? '') === $currency)>
                                {{ $currency }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <button class="app-button" type="submit">Filtriraj</button>
                <a href="{{ route('procurements.index') }}" class="app-button-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="app-card p-4">
        <div class="overflow-x-auto">
            <table class="app-table w-full">
                <thead>
                    <tr>
                        <th>Naslov</th>
                        <th>Partner</th>
                        <th>Referenca</th>
                        <th>Status</th>
                        <th>Nabavna</th>
                        <th>Prodajna</th>
                        <th>FX</th>
                        <th>Datum</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $item->title }}</div>
                                @if($item->notes)
                                    <div class="text-xs app-muted mt-1">
                                        {{ \Illuminate\Support\Str::limit($item->notes, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td>{{ $item->partner->name ?? '-' }}</td>

                            <td>{{ $item->reference_no ?: '-' }}</td>

                            <td>{{ $statuses[$item->status] ?? $item->status }}</td>

                            <td>{{ $item->default_purchase_currency }}</td>

                            <td>{{ $item->default_sale_currency }}</td>

                            <td>
                                <div class="text-xs">
                                    EUR/USD: {{ number_format((float) $item->fx_eur_to_usd, 4, ',', '.') }}
                                </div>
                                <div class="text-xs app-muted">
                                    USD/EUR: {{ number_format((float) $item->fx_usd_to_eur, 4, ',', '.') }}
                                </div>
                            </td>

                            <td>
                                {{ $item->offer_date ? $item->offer_date->format('d.m.Y') : $item->created_at->format('d.m.Y') }}
                            </td>

                            <td class="text-right">
                                <a href="{{ route('procurements.edit', $item) }}" class="app-link">
                                    Otvori
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="app-muted">
                                Nema kalkulacija.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($items, 'links'))
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

@endsection