@extends('layouts.app')

@section('title', 'Uredi kalkulaciju')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">{{ $item->title }}</h2>
            <div class="text-sm app-muted mt-1">
                Osnovni dokument / priprema za items UI
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('procurements.index') }}" class="app-button-secondary">
                Natrag
            </a>

            <form method="POST" action="{{ route('procurements.destroy', $item) }}" onsubmit="return confirm('Obrisati kalkulaciju?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="app-button-secondary">Obriši</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="app-card p-4 mb-4">
            <div class="text-sm">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="app-card p-4 mb-4">
            <div class="font-medium mb-2">Provjeri unos:</div>
            <ul class="text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>— {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2">
            <div class="app-card p-6">
                <h3 class="font-semibold mb-4">Osnovni podaci</h3>

                <form method="POST" action="{{ route('procurements.update', $item) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="app-form-group md:col-span-2">
                            <label class="app-label">Partner *</label>
                            <select name="partner_id" class="app-select" required>
                                <option value="">-- odaberi partnera --</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}" @selected(old('partner_id', $item->partner_id) == $partner->id)>
                                        {{ $partner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="app-form-group md:col-span-2">
                            <label class="app-label">Naziv kalkulacije *</label>
                            <input name="title" class="app-input" value="{{ old('title', $item->title) }}" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Referentni broj</label>
                            <input name="reference_no" class="app-input" value="{{ old('reference_no', $item->reference_no) }}">
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Status *</label>
                            <select name="status" class="app-select" required>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $item->status) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Datum ponude</label>
                            <input
                                type="date"
                                name="offer_date"
                                class="app-input"
                                value="{{ old('offer_date', optional($item->offer_date)->format('Y-m-d')) }}"
                            >
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Vrijedi do</label>
                            <input
                                type="date"
                                name="valid_until"
                                class="app-input"
                                value="{{ old('valid_until', optional($item->valid_until)->format('Y-m-d')) }}"
                            >
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Prodajna valuta *</label>
                            <select name="default_sale_currency" class="app-select" required>
                                <option value="EUR" @selected(old('default_sale_currency', $item->default_sale_currency) === 'EUR')>EUR</option>
                                <option value="USD" @selected(old('default_sale_currency', $item->default_sale_currency) === 'USD')>USD</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Nabavna valuta *</label>
                            <select name="default_purchase_currency" class="app-select" required>
                                <option value="EUR" @selected(old('default_purchase_currency', $item->default_purchase_currency) === 'EUR')>EUR</option>
                                <option value="USD" @selected(old('default_purchase_currency', $item->default_purchase_currency) === 'USD')>USD</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">FX EUR → USD *</label>
                            <input
                                type="number"
                                step="0.000001"
                                min="0.000001"
                                name="fx_eur_to_usd"
                                class="app-input"
                                value="{{ old('fx_eur_to_usd', number_format((float) $item->fx_eur_to_usd, 6, '.', '')) }}"
                                required
                            >
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">FX USD → EUR *</label>
                            <input
                                type="number"
                                step="0.000001"
                                min="0.000001"
                                name="fx_usd_to_eur"
                                class="app-input"
                                value="{{ old('fx_usd_to_eur', number_format((float) $item->fx_usd_to_eur, 6, '.', '')) }}"
                                required
                            >
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Default PDV (%) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                name="vat_rate"
                                class="app-input"
                                value="{{ old('vat_rate', number_format((float) $item->vat_rate, 2, '.', '')) }}"
                                required
                            >
                        </div>

                        <div class="app-form-group md:col-span-2">
                            <label class="app-label">Napomena</label>
                            <textarea name="notes" rows="4" class="app-input">{{ old('notes', $item->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button class="app-button" type="submit">Spremi promjene</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="app-card p-6">
                <h3 class="font-semibold mb-4">Sažetak</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <div class="app-muted">Partner</div>
                        <div>{{ $item->partner->name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="app-muted">Status</div>
                        <div>{{ $statuses[$item->status] ?? $item->status }}</div>
                    </div>

                    <div>
                        <div class="app-muted">Valute</div>
                        <div>{{ $item->default_purchase_currency }} → {{ $item->default_sale_currency }}</div>
                    </div>

                    <div>
                        <div class="app-muted">FX snapshot</div>
                        <div>EUR/USD: {{ number_format((float) $item->fx_eur_to_usd, 4, ',', '.') }}</div>
                        <div>USD/EUR: {{ number_format((float) $item->fx_usd_to_eur, 4, ',', '.') }}</div>
                    </div>

                    <div>
                        <div class="app-muted">Datum kreiranja</div>
                        <div>{{ $item->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="app-card p-6">
                <h3 class="font-semibold mb-4">Items / troškovi</h3>

                <div class="text-sm app-muted">
                    Sljedeći korak:
                    items grid,
                    odvojeni troškovi terena,
                    neto/bruto/marža/PDV efekt.
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="border rounded-lg p-3">
                        <div class="app-muted">Stavke</div>
                        <div class="font-semibold">{{ $item->items->count() }}</div>
                    </div>

                    <div class="border rounded-lg p-3">
                        <div class="app-muted">Troškovi</div>
                        <div class="font-semibold">{{ $item->costs->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection