@extends('layouts.app')

@section('title', 'Uredi pristup')

@section('content')

<div class="max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Uredi pristup</h2>

        <a href="{{ route('partners.show', $item->partner_id) }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form method="POST" action="{{ route('credentials.update', $item) }}" class="app-card p-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="partner_id" value="{{ old('partner_id', $item->partner_id) }}">

        <div class="app-form-group">
            <label class="app-label">Naziv *</label>
            <input name="title" class="app-input" value="{{ old('title', $item->title) }}" required>
        </div>

        <div class="app-form-group">
            <label class="app-label">Tip pristupa *</label>
            <select name="credential_type" class="app-select" required>
                <option value="">-- odaberi --</option>
                <option value="cpanel" {{ old('credential_type', $item->credential_type) === 'cpanel' ? 'selected' : '' }}>cPanel</option>
                <option value="wordpress" {{ old('credential_type', $item->credential_type) === 'wordpress' ? 'selected' : '' }}>WordPress</option>
                <option value="server" {{ old('credential_type', $item->credential_type) === 'server' ? 'selected' : '' }}>Server</option>
                <option value="router" {{ old('credential_type', $item->credential_type) === 'router' ? 'selected' : '' }}>Router / Firewall</option>
                <option value="email" {{ old('credential_type', $item->credential_type) === 'email' ? 'selected' : '' }}>Email</option>
                <option value="license" {{ old('credential_type', $item->credential_type) === 'license' ? 'selected' : '' }}>Licenca</option>
                <option value="other" {{ old('credential_type', $item->credential_type) === 'other' ? 'selected' : '' }}>Ostalo</option>
            </select>
        </div>

        <div class="app-form-group">
            <label class="app-label">Korisničko ime</label>
            <input name="username" class="app-input" value="{{ old('username', $item->username) }}">
        </div>

        <div class="app-form-group">
            <label class="app-label">Nova lozinka</label>
            <input name="password" class="app-input" value="">
        </div>

        <div class="app-form-group">
            <label class="app-label">URL</label>
            <input name="url" class="app-input" value="{{ old('url', $item->url) }}">
        </div>

        <div class="app-form-group">
            <label class="app-label">Bilješke</label>
            <textarea name="notes" class="app-textarea">{{ old('notes', $item->notes) }}</textarea>
        </div>

        <div class="app-form-group">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                <span>Aktivan pristup</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button class="app-button">Spremi izmjene</button>
            <a href="{{ route('partners.show', $item->partner_id) }}" class="app-button-secondary">Odustani</a>
        </div>
    </form>
</div>

@endsection