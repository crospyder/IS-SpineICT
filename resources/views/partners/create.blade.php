@extends('layouts.app')

@section('title', 'Novi partner')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Dodaj partnera</h2>

        <a
            href="{{ $returnTo ?: route('partners.index') }}"
            class="app-button-secondary"
        >
            Natrag
        </a>
    </div>

    <form action="{{ route('partners.store') }}" method="POST" class="space-y-6" id="partner-create-form">
        @csrf

        <input type="hidden" name="return_to" value="{{ $returnTo }}">
        <input type="hidden" name="return_partner_field" value="{{ $returnPartnerField }}">

        @if (session('status'))
            <div class="app-card p-4">
                <div class="text-sm">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="app-card p-4">
                <div class="app-badge badge-overdue mb-3">Provjeri unesene podatke.</div>

                <ul class="text-sm app-muted space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>— {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="app-card p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold">Dohvat iz Sudskog registra</h3>
                <div class="text-sm app-muted mt-1">
                    Unesi OIB i povuci službene podatke partnera. Email, telefon, website i bilješke ostaju lokalni operativni podaci.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="app-form-group md:col-span-4">
                    <label class="app-label" for="oib">OIB</label>
                    <input
                        type="text"
                        id="oib"
                        name="oib"
                        class="app-input"
                        value="{{ old('oib') }}"
                        inputmode="numeric"
                        maxlength="11"
                    >
                </div>

                <div class="md:col-span-4 flex gap-3">
                    <button type="button" id="lookup-partner-by-oib" class="app-button">
                        Dohvati iz registra
                    </button>

                    <button type="button" id="clear-registry-fill" class="app-button-secondary">
                        Očisti
                    </button>
                </div>

                <div class="md:col-span-4">
                    <div id="registry-status" class="text-sm app-muted"></div>
                </div>
            </div>

            <div id="registry-existing-partner" class="mt-4 hidden">
                <div class="app-badge badge-overdue">
                    Partner s tim OIB-om već postoji u sustavu.
                </div>

                <div class="mt-2 text-sm">
                    <a href="#" id="registry-existing-partner-link" class="app-link">Otvori postojeći zapis</a>
                </div>
            </div>
        </div>

        <div class="app-card p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold">Osnovni podaci</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="name">Naziv *</label>
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name') }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="legal_name">Pravni naziv</label>
                    <input type="text" id="legal_name" name="legal_name" class="app-input" value="{{ old('legal_name') }}">
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
            </div>
        </div>

        <div class="app-card p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold">Ugovorni klijent</h3>
                <div class="text-sm app-muted mt-1">
                    Označi ako partner ima aktivan ugovorni odnos i odaberi koje usluge pružate.
                </div>
            </div>

            <div class="space-y-5">
                <div class="app-form-group">
                    <label class="inline-flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="is_contract_client"
                            id="is_contract_client"
                            value="1"
                            {{ old('is_contract_client') ? 'checked' : '' }}
                        >
                        <span>Ovo je ugovorni klijent</span>
                    </label>
                </div>

                <div id="contract-fields" class="{{ old('is_contract_client') ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="app-form-group">
                            <label class="app-label" for="contract_status">Status ugovora</label>
                            <select id="contract_status" name="contract_status" class="app-input">
                                <option value="">-- Odaberi status --</option>
                                <option value="active" {{ old('contract_status') === 'active' ? 'selected' : '' }}>Aktivan</option>
                                <option value="pending" {{ old('contract_status') === 'pending' ? 'selected' : '' }}>U pripremi</option>
                                <option value="paused" {{ old('contract_status') === 'paused' ? 'selected' : '' }}>Pauziran</option>
                                <option value="expired" {{ old('contract_status') === 'expired' ? 'selected' : '' }}>Istekao</option>
                            </select>
                        </div>

                        <div></div>

                        <div class="app-form-group">
                            <label class="app-label" for="contract_start_date">Početak ugovora</label>
                            <input
                                type="date"
                                id="contract_start_date"
                                name="contract_start_date"
                                class="app-input"
                                value="{{ old('contract_start_date') }}"
                            >
                        </div>

                        <div class="app-form-group">
                            <label class="app-label" for="contract_end_date">Završetak ugovora</label>
                            <input
                                type="date"
                                id="contract_end_date"
                                name="contract_end_date"
                                class="app-input"
                                value="{{ old('contract_end_date') }}"
                            >
                        </div>

                        <div class="app-form-group md:col-span-2">
                            <label class="app-label" for="contract_notes">Bilješke uz ugovor</label>
                            <textarea id="contract_notes" name="contract_notes" rows="3" class="app-textarea">{{ old('contract_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="text-sm font-medium mb-3">Ugovorne usluge</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($contractServiceTypes as $serviceType)
                                <label class="app-card p-4 flex items-start gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="contract_service_ids[]"
                                        value="{{ $serviceType->id }}"
                                        class="mt-1"
                                        {{ in_array($serviceType->id, old('contract_service_ids', [])) ? 'checked' : '' }}
                                    >

                                    <div>
                                        <div class="font-medium">{{ $serviceType->name }}</div>

                                        @if ($serviceType->description)
                                            <div class="text-sm app-muted mt-1">
                                                {{ $serviceType->description }}
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold">Dodatno</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="app-input" value="{{ old('email') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" class="app-input" value="{{ old('phone') }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="website">Website</label>
                    <input type="text" id="website" name="website" class="app-input" value="{{ old('website') }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="notes">Bilješke</label>
                    <textarea id="notes" name="notes" rows="4" class="app-textarea">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="app-button">Spremi</button>
                <a href="{{ $returnTo ?: route('partners.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const lookupUrl = @json(route('partners.lookup-by-oib'));
    const csrfToken = @json(csrf_token());

    const oibInput = document.getElementById('oib');
    const nameInput = document.getElementById('name');
    const legalNameInput = document.getElementById('legal_name');
    const addressInput = document.getElementById('address');
    const cityInput = document.getElementById('city');
    const postalCodeInput = document.getElementById('postal_code');
    const countryInput = document.getElementById('country');

    const statusBox = document.getElementById('registry-status');
    const existingPartnerBox = document.getElementById('registry-existing-partner');
    const existingPartnerLink = document.getElementById('registry-existing-partner-link');

    const lookupButton = document.getElementById('lookup-partner-by-oib');
    const clearButton = document.getElementById('clear-registry-fill');

    const contractClientCheckbox = document.getElementById('is_contract_client');
    const contractFields = document.getElementById('contract-fields');

    const toggleContractFields = () => {
        if (contractClientCheckbox.checked) {
            contractFields.classList.remove('hidden');
        } else {
            contractFields.classList.add('hidden');
        }
    };

    const setStatus = (message, isError = false) => {
        statusBox.textContent = message || '';
        statusBox.className = isError ? 'text-sm text-red-400' : 'text-sm app-muted';
    };

    const fillPartnerFields = (partner) => {
        nameInput.value = partner.name ?? '';
        legalNameInput.value = partner.legal_name ?? '';
        addressInput.value = partner.address ?? '';
        cityInput.value = partner.city ?? '';
        postalCodeInput.value = partner.postal_code ?? '';
        countryInput.value = partner.country ?? 'Hrvatska';
        oibInput.value = partner.oib ?? oibInput.value;
    };

    const clearRegistryFields = () => {
        nameInput.value = '';
        legalNameInput.value = '';
        addressInput.value = '';
        cityInput.value = '';
        postalCodeInput.value = '';
        countryInput.value = 'Hrvatska';
        setStatus('');
        existingPartnerBox.classList.add('hidden');
        existingPartnerLink.setAttribute('href', '#');
    };

    lookupButton.addEventListener('click', async () => {
        existingPartnerBox.classList.add('hidden');
        existingPartnerLink.setAttribute('href', '#');

        const oib = (oibInput.value || '').trim();

        if (!oib) {
            setStatus('Unesi OIB prije dohvaćanja.', true);
            return;
        }

        setStatus('Dohvaćam podatke iz Sudskog registra...');

        try {
            const response = await fetch(lookupUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ oib }),
            });

            const data = await response.json();

            if (!response.ok || !data.ok) {
                setStatus(data.message || 'Greška pri dohvaćanju podataka.', true);
                return;
            }

            fillPartnerFields(data.partner || {});
            setStatus(data.message || 'Podaci su dohvaćeni.');

            if (data.existing_partner?.edit_url) {
                existingPartnerBox.classList.remove('hidden');
                existingPartnerLink.setAttribute('href', data.existing_partner.edit_url);
                existingPartnerLink.textContent = `Otvori postojeći zapis: ${data.existing_partner.name}`;
            }
        } catch (error) {
            setStatus('Greška pri komunikaciji sa serverom.', true);
        }
    });

    clearButton.addEventListener('click', () => {
        clearRegistryFields();
    });

    contractClientCheckbox.addEventListener('change', toggleContractFields);
    toggleContractFields();
});
</script>

@endsection