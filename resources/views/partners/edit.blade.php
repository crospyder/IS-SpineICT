@extends('layouts.app')

@section('title', 'Uredi partnera')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Uredi partnera</h2>

        <a href="{{ route('partners.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('partners.update', $partner) }}" method="POST" class="space-y-6" id="partner-edit-form">
        @csrf
        @method('PUT')

        @php
            $selectedContractServiceIds = old(
                'contract_service_ids',
                $partner->contractServices->pluck('id')->all()
            );
        @endphp

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
                <h3 class="text-base font-semibold">Sudski registar</h3>
                <div class="text-sm app-muted mt-1">
                    Osvježava samo službene podatke: naziv, pravni naziv, OIB, adresa, grad, poštanski broj i državu.
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
                        value="{{ old('oib', $partner->oib) }}"
                        inputmode="numeric"
                        maxlength="11"
                    >
                </div>

                <div class="md:col-span-4 flex gap-3">
                    <button type="button" id="refresh-from-sudreg" class="app-button">
                        Osvježi iz registra
                    </button>
                </div>

                <div class="md:col-span-4">
                    <div id="registry-status" class="text-sm app-muted"></div>
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
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name', $partner->name) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="legal_name">Pravni naziv</label>
                    <input type="text" id="legal_name" name="legal_name" class="app-input" value="{{ old('legal_name', $partner->legal_name) }}">
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
            </div>
        </div>

        <div class="app-card p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold">Ugovorni klijent</h3>
                <div class="text-sm app-muted mt-1">
                    Označi ugovorni odnos, status i odaberi koje usluge su pokrivene ugovorom.
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
                            {{ old('is_contract_client', $partner->is_contract_client) ? 'checked' : '' }}
                        >
                        <span>Ovo je ugovorni klijent</span>
                    </label>
                </div>

                <div id="contract-fields" class="{{ old('is_contract_client', $partner->is_contract_client) ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="app-form-group">
                            <label class="app-label" for="contract_status">Status ugovora</label>
                            <select id="contract_status" name="contract_status" class="app-input">
                                <option value="">-- Odaberi status --</option>
                                <option value="active" {{ old('contract_status', $partner->contract_status) === 'active' ? 'selected' : '' }}>Aktivan</option>
                                <option value="pending" {{ old('contract_status', $partner->contract_status) === 'pending' ? 'selected' : '' }}>U pripremi</option>
                                <option value="paused" {{ old('contract_status', $partner->contract_status) === 'paused' ? 'selected' : '' }}>Pauziran</option>
                                <option value="expired" {{ old('contract_status', $partner->contract_status) === 'expired' ? 'selected' : '' }}>Istekao</option>
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
                                value="{{ old('contract_start_date', optional($partner->contract_start_date)->format('Y-m-d')) }}"
                            >
                        </div>

                        <div class="app-form-group">
                            <label class="app-label" for="contract_end_date">Završetak ugovora</label>
                            <input
                                type="date"
                                id="contract_end_date"
                                name="contract_end_date"
                                class="app-input"
                                value="{{ old('contract_end_date', optional($partner->contract_end_date)->format('Y-m-d')) }}"
                            >
                        </div>

                        <div class="app-form-group md:col-span-2">
                            <label class="app-label" for="contract_notes">Bilješke uz ugovor</label>
                            <textarea id="contract_notes" name="contract_notes" rows="3" class="app-textarea">{{ old('contract_notes', $partner->contract_notes) }}</textarea>
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
                                        {{ in_array($serviceType->id, $selectedContractServiceIds) ? 'checked' : '' }}
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
        <h3 class="text-base font-semibold">Inventory / Agent sync</h3>
        <div class="text-sm app-muted mt-1">
            Uključi inventory za partnera i definiraj koristi li ručni unos, agent sync ili oboje.
        </div>
    </div>

    <div class="space-y-5">
        <div class="flex flex-wrap gap-6">
            <label class="inline-flex items-center gap-2">
                <input
                    type="checkbox"
                    name="inventory_enabled"
                    id="inventory_enabled"
                    value="1"
                    {{ old('inventory_enabled', $partner->inventory_enabled) ? 'checked' : '' }}
                >
                <span>Inventory uključen</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input
                    type="checkbox"
                    name="is_internal"
                    value="1"
                    {{ old('is_internal', $partner->is_internal) ? 'checked' : '' }}
                >
                <span>Interni sustav</span>
            </label>
        </div>

        <div id="inventory-fields" class="{{ old('inventory_enabled', $partner->inventory_enabled) ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="inventory_mode">Inventory način rada</label>
                    <select id="inventory_mode" name="inventory_mode" class="app-input">
                        <option value="">-- Odaberi način --</option>
                        <option value="manual" {{ old('inventory_mode', $partner->inventory_mode) === 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="agent" {{ old('inventory_mode', $partner->inventory_mode) === 'agent' ? 'selected' : '' }}>Agent</option>
                        <option value="hybrid" {{ old('inventory_mode', $partner->inventory_mode) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="inventory_partner_key">Inventory partner key</label>
                    <input
                        type="text"
                        id="inventory_partner_key"
                        name="inventory_partner_key"
                        class="app-input"
                        value="{{ old('inventory_partner_key', $partner->inventory_partner_key) }}"
                        placeholder="npr. TEST-INV-001"
                    >
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
                    <input type="email" id="email" name="email" class="app-input" value="{{ old('email', $partner->email) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" class="app-input" value="{{ old('phone', $partner->phone) }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="website">Website</label>
                    <input type="text" id="website" name="website" class="app-input" value="{{ old('website', $partner->website) }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="notes">Bilješke</label>
                    <textarea id="notes" name="notes" rows="4" class="app-textarea">{{ old('notes', $partner->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="app-button">Spremi izmjene</button>
                <a href="{{ route('partners.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const refreshUrl = @json(route('partners.refresh-from-sudreg', $partner));
    const csrfToken = @json(csrf_token());

    const oibInput = document.getElementById('oib');
    const nameInput = document.getElementById('name');
    const legalNameInput = document.getElementById('legal_name');
    const addressInput = document.getElementById('address');
    const cityInput = document.getElementById('city');
    const postalCodeInput = document.getElementById('postal_code');
    const countryInput = document.getElementById('country');
    const statusBox = document.getElementById('registry-status');
    const refreshButton = document.getElementById('refresh-from-sudreg');
    const inventoryEnabledCheckbox = document.getElementById('inventory_enabled');
const inventoryFields = document.getElementById('inventory-fields');

const toggleInventoryFields = () => {
    if (inventoryEnabledCheckbox.checked) {
        inventoryFields.classList.remove('hidden');
    } else {
        inventoryFields.classList.add('hidden');
    }
};

inventoryEnabledCheckbox.addEventListener('change', toggleInventoryFields);
toggleInventoryFields();
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

    const fillOfficialFields = (partner) => {
        nameInput.value = partner.name ?? '';
        legalNameInput.value = partner.legal_name ?? '';
        addressInput.value = partner.address ?? '';
        cityInput.value = partner.city ?? '';
        postalCodeInput.value = partner.postal_code ?? '';
        countryInput.value = partner.country ?? 'Hrvatska';
        oibInput.value = partner.oib ?? oibInput.value;
    };

    refreshButton.addEventListener('click', async () => {
        const oib = (oibInput.value || '').trim();

        if (!oib) {
            setStatus('Partner mora imati OIB za osvježavanje iz registra.', true);
            return;
        }

        setStatus('Osvježavam službene podatke iz Sudskog registra...');

        try {
            const response = await fetch(refreshUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({}),
            });

            const data = await response.json();

            if (!response.ok || !data.ok) {
                setStatus(data.message || 'Greška pri osvježavanju podataka.', true);
                return;
            }

            fillOfficialFields(data.partner || {});
            setStatus(data.message || 'Podaci su osvježeni.');
        } catch (error) {
            setStatus('Greška pri komunikaciji sa serverom.', true);
        }
    });

    contractClientCheckbox.addEventListener('change', toggleContractFields);
    toggleContractFields();
});
</script>

@endsection