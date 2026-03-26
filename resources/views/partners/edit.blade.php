@extends('layouts.app')

@section('title', 'Uredi partnera')

@section('content')

<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Uredi partnera</h2>

        <a href="{{ route('partners.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form action="{{ route('partners.update', $partner) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="name">Naziv *</label>
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name', $partner->name) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="legal_name">Pravni naziv</label>
                    <input type="text" id="legal_name" name="legal_name" class="app-input" value="{{ old('legal_name', $partner->legal_name) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="oib">OIB</label>
                    <input type="text" id="oib" name="oib" class="app-input" value="{{ old('oib', $partner->oib) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="app-input" value="{{ old('email', $partner->email) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" class="app-input" value="{{ old('phone', $partner->phone) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="website">Website</label>
                    <input type="text" id="website" name="website" class="app-input" value="{{ old('website', $partner->website) }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="address">Adresa</label>
                    <input type="text" id="address" name="address" class="app-input" value="{{ old('address', $partner->address) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="city">Grad</label>
                    <input type="text" id="city" name="city" class="app-input" value="{{ old('city', $partner->city) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="postal_code">Poštanski broj</label>
                    <input type="text" id="postal_code" name="postal_code" class="app-input" value="{{ old('postal_code', $partner->postal_code) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="country">Država</label>
                    <input type="text" id="country" name="country" class="app-input" value="{{ old('country', $partner->country) }}">
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $partner->is_active) ? 'checked' : '' }}>
                        <span>Aktivan</span>
                    </label>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="notes">Bilješke</label>
                    <textarea id="notes" name="notes" rows="4" class="app-textarea">{{ old('notes', $partner->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">Spremi izmjene</button>
                <a href="{{ route('partners.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
</div>

@endsection