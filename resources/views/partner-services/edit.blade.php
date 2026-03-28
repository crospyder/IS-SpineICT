@extends('layouts.app')

@section('title', 'Uredi uslugu')

@section('content')

<div class="max-w-5xl space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">Uredi uslugu</h2>
            <div class="text-sm app-muted mt-1">
                {{ $partnerService->name }}
            </div>
        </div>

        <a href="{{ route('partner-services.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('partner-services.update', $partnerService) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- OSNOVNI PODACI --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Osnovni podaci</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Partner *</label>
                    <select name="partner_id" class="app-select" required>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}"
                                @selected(old('partner_id', $partnerService->partner_id) == $partner->id)>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Tip usluge *</label>
                    <input
                        type="text"
                        name="service_type"
                        class="app-input"
                        value="{{ old('service_type', $partnerService->service_type) }}"
                        required
                    >
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Naziv *</label>
                    <input
                        type="text"
                        name="name"
                        class="app-input"
                        value="{{ old('name', $partnerService->name) }}"
                        required
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Domena</label>
                    <input
                        type="text"
                        name="domain_name"
                        class="app-input"
                        value="{{ old('domain_name', $partnerService->domain_name) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Provider</label>
                    <input
                        type="text"
                        name="provider"
                        class="app-input"
                        value="{{ old('provider', $partnerService->provider) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Registrar</label>
                    <input
                        type="text"
                        name="registrar"
                        class="app-input"
                        value="{{ old('registrar', $partnerService->registrar) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Status</label>
                    <input
                        type="text"
                        name="status"
                        class="app-input"
                        value="{{ old('status', $partnerService->status) }}"
                        required
                    >
                </div>

            </div>
        </div>

        {{-- DATUMI I STATUS --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Datumi i status</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Početak</label>
                    <input
                        type="date"
                        name="starts_on"
                        class="app-input"
                        value="{{ old('starts_on', optional($partnerService->starts_on)->format('Y-m-d')) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Ističe</label>
                    <input
                        type="date"
                        name="expires_on"
                        class="app-input"
                        value="{{ old('expires_on', optional($partnerService->expires_on)->format('Y-m-d')) }}"
                    >
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $partnerService->is_active) ? 'checked' : '' }}
                        >
                        Aktivna usluga
                    </label>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Datum zadnje obnove</label>
                    <input
                        type="date"
                        name="renewal_date"
                        class="app-input"
                        value="{{ old('renewal_date', optional($partnerService->renewal_date)->format('Y-m-d')) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Period obnove</label>
                    <input
                        type="text"
                        name="renewal_period"
                        class="app-input"
                        value="{{ old('renewal_period', $partnerService->renewal_period) }}"
                        placeholder="npr. monthly, yearly"
                    >
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            id="auto_renew"
                            name="auto_renew"
                            value="1"
                            {{ old('auto_renew', $partnerService->auto_renew) ? 'checked' : '' }}
                        >
                        Automatska obnova
                    </label>
                </div>

            </div>
        </div>

        {{-- DODATNO --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Dodatno</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Admin link</label>
                    <input
                        type="text"
                        name="admin_link"
                        class="app-input"
                        value="{{ old('admin_link', $partnerService->admin_link) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label">Način obnove</label>
                    <input
                        type="text"
                        name="renewal_method"
                        class="app-input"
                        value="{{ old('renewal_method', $partnerService->renewal_method) }}"
                    >
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Napomene</label>
                    <textarea name="notes" rows="4" class="app-textarea">{{ old('notes', $partnerService->notes) }}</textarea>
                </div>

            </div>
        </div>

        {{-- ERRORS --}}
        @if ($errors->any())
            <div class="app-card p-4 border border-red-500/30 bg-red-500/10">
                <div class="font-medium mb-2">Greška:</div>
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>— {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ACTIONS --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('partner-services.index') }}" class="app-link text-sm">
                Odustani
            </a>

            <button type="submit" class="app-button">
                Spremi izmjene
            </button>
        </div>

    </form>

</div>

@endsection