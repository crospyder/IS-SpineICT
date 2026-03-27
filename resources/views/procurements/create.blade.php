@extends('layouts.app')

@section('title', 'Nova kalkulacija')

@section('content')

<div class="max-w-4xl">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Nova kalkulacija</h2>

        <a href="{{ route('procurements.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form method="POST" action="{{ route('procurements.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Partner *</label>
                    <select name="partner_id" class="app-select" required>
                        <option value="">-- odaberi --</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}">
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Naziv *</label>
                    <input name="title" class="app-input" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Prodajna valuta</label>
                    <select name="default_sale_currency" class="app-select">
                        <option value="EUR">EUR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Nabavna valuta</label>
                    <select name="default_purchase_currency" class="app-select">
                        <option value="EUR">EUR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">EUR → USD</label>
                    <input name="fx_eur_to_usd" class="app-input" value="1.08">
                </div>

                <div class="app-form-group">
                    <label class="app-label">USD → EUR</label>
                    <input name="fx_usd_to_eur" class="app-input" value="0.92">
                </div>

                <div class="app-form-group">
                    <label class="app-label">PDV (%)</label>
                    <input name="vat_rate" class="app-input" value="25">
                </div>

            </div>

            <div class="mt-6">
                <button class="app-button">Spremi</button>
            </div>
        </form>
    </div>

</div>

@endsection