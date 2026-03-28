@extends('layouts.app')

@section('title', 'Nova usluga')

@section('content')

<div class="max-w-5xl space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">Nova usluga</h2>
            <div class="text-sm app-muted mt-1">
                Evidencija usluge / pretplate
            </div>
        </div>

        <a href="{{ route('partner-services.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('partner-services.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- OSNOVNI PODACI --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Osnovni podaci</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- PARTNER --}}
                <div class="app-form-group">
                    <div class="flex items-center justify-between mb-2">
                        <label class="app-label mb-0">Partner *</label>

                        <a
                            href="{{ route('partners.create', ['return_to' => url()->current(), 'return_partner_field' => 'partner_id']) }}"
                            class="app-link text-sm"
                        >
                            + Novi partner
                        </a>
                    </div>

                    <select id="partner_id" name="partner_id" class="app-select" required>
                        <option value="">-- odaberi --</option>
                        @foreach($partners as $partner)
                            <option
                                value="{{ $partner->id }}"
                                {{ (string) old('partner_id', request('partner_id')) === (string) $partner->id ? 'selected' : '' }}
                            >
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TIP --}}
                <div class="app-form-group">
                    <label class="app-label">Tip usluge</label>
                    <input type="text" name="type" class="app-input" value="{{ old('type') }}" placeholder="npr. Hosting, Domena, Licenca">
                </div>

                {{-- NAZIV --}}
                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Naziv *</label>
                    <input type="text" name="name" class="app-input" value="{{ old('name') }}" required>
                </div>

                {{-- PROVIDER --}}
                <div class="app-form-group">
                    <label class="app-label">Provider</label>
                    <input type="text" name="provider" class="app-input" value="{{ old('provider') }}">
                </div>

                {{-- IDENTIFIKATOR --}}
                <div class="app-form-group">
                    <label class="app-label">Identifikator / domena</label>
                    <input type="text" name="identifier" class="app-input" value="{{ old('identifier') }}">
                </div>

            </div>
        </div>

        {{-- DATUMI I STATUS --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Datumi i status</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Početak</label>
                    <input type="date" name="starts_on" class="app-input" value="{{ old('starts_on') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label">Ističe</label>
                    <input type="date" name="expires_on" class="app-input" value="{{ old('expires_on') }}">
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        Aktivna usluga
                    </label>
                </div>

            </div>
        </div>

        {{-- OBNOVA --}}
        <div class="app-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="text-sm font-semibold">Obnova</div>

                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" id="auto_renew" name="auto_renew" value="1" {{ old('auto_renew') ? 'checked' : '' }}>
                    Automatska obnova
                </label>
            </div>

            <div id="renewFields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ old('auto_renew') ? '' : 'hidden' }}">

                <div class="app-form-group">
                    <label class="app-label">Period</label>
                    <select name="renewal_period" class="app-select">
                        <option value="">-- odaberi --</option>
                        <option value="monthly" {{ old('renewal_period') === 'monthly' ? 'selected' : '' }}>Mjesečno</option>
                        <option value="yearly" {{ old('renewal_period') === 'yearly' ? 'selected' : '' }}>Godišnje</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Podsjetnik</label>
                    <select name="remind_days_before" class="app-select">
                        <option value="">-- bez podsjetnika --</option>
                        <option value="7" {{ old('remind_days_before') === '7' ? 'selected' : '' }}>7 dana</option>
                        <option value="14" {{ old('remind_days_before') === '14' ? 'selected' : '' }}>14 dana</option>
                        <option value="30" {{ old('remind_days_before') === '30' ? 'selected' : '' }}>30 dana</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- NAPOMENE --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Napomene</div>

            <textarea name="notes" rows="4" class="app-textarea">{{ old('notes') }}</textarea>
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
                Spremi uslugu
            </button>
        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const autoRenew = document.getElementById('auto_renew');
    const renewFields = document.getElementById('renewFields');

    function toggleRenew() {
        renewFields.classList.toggle('hidden', !autoRenew.checked);
    }

    autoRenew.addEventListener('change', toggleRenew);

    toggleRenew();
});
</script>

@endsection