@extends('layouts.app')

@section('title', 'Nova kalkulacija')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">Nova kalkulacija</h2>
            <div class="text-sm app-muted mt-1">
                Osnovni dokument + partner + valute + FX snapshot
            </div>
        </div>

        <a href="{{ route('procurements.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

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

    <div class="app-card p-6">
        <form method="POST" action="{{ route('procurements.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Partner *</label>
                    <select name="partner_id" class="app-select" required>
                        <option value="">-- odaberi partnera --</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" @selected(old('partner_id') == $partner->id)>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Naziv kalkulacije *</label>
                    <input name="title" class="app-input" value="{{ old('title') }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Referentni broj</label>
                    <input name="reference_no" class="app-input" value="{{ old('reference_no') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label">Status</label>
                    <select name="status" class="app-select">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'draft') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Datum ponude</label>
                    <input type="date" name="offer_date" class="app-input" value="{{ old('offer_date') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label">Vrijedi do</label>
                    <input type="date" name="valid_until" class="app-input" value="{{ old('valid_until') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label">Prodajna valuta *</label>
                    <select name="default_sale_currency" class="app-select" required>
                        <option value="EUR" @selected(old('default_sale_currency', 'EUR') === 'EUR')>EUR</option>
                        <option value="USD" @selected(old('default_sale_currency') === 'USD')>USD</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Nabavna valuta *</label>
                    <select name="default_purchase_currency" class="app-select" required>
                        <option value="EUR" @selected(old('default_purchase_currency', 'EUR') === 'EUR')>EUR</option>
                        <option value="USD" @selected(old('default_purchase_currency') === 'USD')>USD</option>
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
                        value="{{ old('fx_eur_to_usd', '1.080000') }}"
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
                        value="{{ old('fx_usd_to_eur', '0.925926') }}"
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
                        value="{{ old('vat_rate', '25.00') }}"
                        required
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Napomena</label>
                    <textarea name="notes" rows="4" class="app-input">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button class="app-button" type="submit">Spremi i otvori</button>
                <a href="{{ route('procurements.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
</div>

@endsection