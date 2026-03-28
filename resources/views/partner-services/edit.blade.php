@extends('layouts.app')

@section('title', 'Uredi uslugu')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Uredi uslugu</h2>

        <a href="{{ route('partner-services.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    @if ($errors->any())
        <div class="app-card p-4 mb-4 border border-red-500/30 bg-red-500/10">
            <div class="font-medium mb-2">Greška pri unosu:</div>
            <ul class="text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>— {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="app-card p-6">
        <form action="{{ route('partner-services.update', $partnerService) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="partner_id">Partner *</label>
                    <select id="partner_id" name="partner_id" class="app-select" required>
                        <option value="">-- odaberi --</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ old('partner_id', $partnerService->partner_id) == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="service_type">Tip usluge *</label>
                    <input type="text" id="service_type" name="service_type" class="app-input" value="{{ old('service_type', $partnerService->service_type) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="name">Naziv *</label>
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name', $partnerService->name) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="domain_name">Domena</label>
                    <input type="text" id="domain_name" name="domain_name" class="app-input" value="{{ old('domain_name', $partnerService->domain_name) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="provider">Provider</label>
                    <input type="text" id="provider" name="provider" class="app-input" value="{{ old('provider', $partnerService->provider) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="registrar">Registrar</label>
                    <input type="text" id="registrar" name="registrar" class="app-input" value="{{ old('registrar', $partnerService->registrar) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="status">Status *</label>
                    <input type="text" id="status" name="status" class="app-input" value="{{ old('status', $partnerService->status) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="renewal_period">Period obnove</label>
                    <input type="text" id="renewal_period" name="renewal_period" class="app-input" value="{{ old('renewal_period', $partnerService->renewal_period) }}" placeholder="npr. mjesečno, godišnje, 12 mjeseci">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="starts_on">Početak</label>
                    <input type="date" id="starts_on" name="starts_on" class="app-input" value="{{ old('starts_on', optional($partnerService->starts_on)->format('Y-m-d') ?? $partnerService->starts_on) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="expires_on">Istek</label>
                    <input type="date" id="expires_on" name="expires_on" class="app-input" value="{{ old('expires_on', optional($partnerService->expires_on)->format('Y-m-d') ?? $partnerService->expires_on) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="renewal_date">Datum zadnje obnove</label>
                    <input type="date" id="renewal_date" name="renewal_date" class="app-input" value="{{ old('renewal_date', optional($partnerService->renewal_date)->format('Y-m-d') ?? $partnerService->renewal_date) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="admin_link">Admin link</label>
                    <input type="text" id="admin_link" name="admin_link" class="app-input" value="{{ old('admin_link', $partnerService->admin_link) }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="renewal_method">Način obnove</label>
                    <input type="text" id="renewal_method" name="renewal_method" class="app-input" value="{{ old('renewal_method', $partnerService->renewal_method) }}">
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="auto_renew" value="1" {{ old('auto_renew', $partnerService->auto_renew) ? 'checked' : '' }}>
                        <span>Auto renew</span>
                    </label>
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $partnerService->is_active) ? 'checked' : '' }}>
                        <span>Aktivna usluga</span>
                    </label>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="notes">Bilješke</label>
                    <textarea id="notes" name="notes" rows="4" class="app-textarea">{{ old('notes', $partnerService->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">Spremi izmjene</button>
                <a href="{{ route('partner-services.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
</div>

@endsection