@extends('layouts.app')

@section('title', 'Uredi korisnika')

@section('content')

<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Uredi korisnika</h2>

        <a href="{{ route('users.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="app-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="name">Ime i prezime *</label>
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="email">Email *</label>
                    <input type="email" id="email" name="email" class="app-input" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="password">Nova lozinka</label>
                    <input type="password" id="password" name="password" class="app-input">
                    <div class="text-xs app-muted mt-1">
                        Ostavi prazno ako ne mijenjaš lozinku.
                    </div>
                </div>

                <div class="app-form-group flex items-end">
                    <div class="space-y-3">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                            <span>Admin</span>
                        </label>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <span>Aktivan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="flex gap-3">
                <button type="submit" class="app-button">Spremi izmjene</button>
                <a href="{{ route('users.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </div>
    </form>
</div>

@endsection