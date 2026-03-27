@extends('layouts.app')

@section('title', 'Novi pristup')

@section('content')

<div class="max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Novi pristup</h2>

        <a
            href="{{ request('partner_id') ? route('partners.show', request('partner_id')) : route('partners.index') }}"
            class="app-button-secondary"
        >
            Natrag
        </a>
    </div>

    @if (!request('partner_id'))
        <div class="mb-4 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm">
            Pristup mora biti vezan uz partnera. Otvori “Novi pristup” iz partner kartona.
        </div>
    @endif

    <form method="POST" action="{{ route('credentials.store') }}" class="app-card p-6">
        @csrf

        <input type="hidden" name="partner_id" value="{{ request('partner_id') }}">

        <div class="app-form-group">
            <label class="app-label">Naziv *</label>
            <input name="title" class="app-input" value="{{ old('title') }}" required>
        </div>

        <div class="app-form-group">
            <label class="app-label">Tip pristupa *</label>
            <select name="credential_type" class="app-select" required>
                <option value="">-- odaberi --</option>
                <option value="cpanel" {{ old('credential_type') === 'cpanel' ? 'selected' : '' }}>cPanel</option>
                <option value="wordpress" {{ old('credential_type') === 'wordpress' ? 'selected' : '' }}>WordPress</option>
                <option value="server" {{ old('credential_type') === 'server' ? 'selected' : '' }}>Server</option>
                <option value="router" {{ old('credential_type') === 'router' ? 'selected' : '' }}>Router / Firewall</option>
                <option value="email" {{ old('credential_type') === 'email' ? 'selected' : '' }}>Email</option>
                <option value="license" {{ old('credential_type') === 'license' ? 'selected' : '' }}>Licenca</option>
                <option value="other" {{ old('credential_type') === 'other' ? 'selected' : '' }}>Ostalo</option>
            </select>
        </div>

        <div class="app-form-group">
            <label class="app-label">Korisničko ime</label>
            <input name="username" class="app-input" value="{{ old('username') }}">
        </div>

        <div class="app-form-group">
            <label class="app-label">Lozinka</label>
            <input name="password" class="app-input" value="{{ old('password') }}">
        </div>

        <div class="app-form-group">
            <label class="app-label">URL</label>
            <input name="url" class="app-input" value="{{ old('url') }}">
        </div>

        <div class="app-form-group">
            <label class="app-label">Bilješke</label>
            <textarea name="notes" class="app-textarea">{{ old('notes') }}</textarea>
        </div>

        <div class="app-form-group">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                <span>Aktivan pristup</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button class="app-button">Spremi</button>
            <a
                href="{{ request('partner_id') ? route('partners.show', request('partner_id')) : route('partners.index') }}"
                class="app-button-secondary"
            >
                Odustani
            </a>
        </div>
    </form>
</div>

@endsection