@extends('layouts.app')

@section('title', 'Novi partner')

@section('content')

<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Dodaj partnera</h2>

        <a
            href="{{ $returnTo ?: route('partners.index') }}"
            class="app-button-secondary"
        >
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form action="{{ route('partners.store') }}" method="POST">
            @csrf

            <input type="hidden" name="return_to" value="{{ $returnTo }}">
            <input type="hidden" name="return_partner_field" value="{{ $returnPartnerField }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="name">Naziv *</label>
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name') }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="legal_name">Pravni naziv</label>
                    <input type="text" id="legal_name" name="legal_name" class="app-input" value="{{ old('legal_name') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="oib">OIB</label>
                    <input type="text" id="oib" name="oib" class="app-input" value="{{ old('oib') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="app-input" value="{{ old('email') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" class="app-input" value="{{ old('phone') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="website">Website</label>
                    <input type="text" id="website" name="website" class="app-input" value="{{ old('website') }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="address">Adresa</label>
                    <input type="text" id="address" name="address" class="app-input" value="{{ old('address') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="city">Grad</label>
                    <input type="text" id="city" name="city" class="app-input" value="{{ old('city') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="postal_code">Poštanski broj</label>
                    <input type="text" id="postal_code" name="postal_code" class="app-input" value="{{ old('postal_code') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="country">Država</label>
                    <input type="text" id="country" name="country" class="app-input" value="{{ old('country', 'Hrvatska') }}">
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <span>Aktivan</span>
                    </label>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="notes">Bilješke</label>
                    <textarea id="notes" name="notes" rows="4" class="app-textarea">{{ old('notes') }}</textarea>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 app-badge badge-overdue">
                    Provjeri unesene podatke.
                </div>
            @endif

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">Spremi</button>
                <a href="{{ $returnTo ?: route('partners.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
</div>

@endsection