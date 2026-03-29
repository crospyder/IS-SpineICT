@extends('layouts.app')

@section('title', 'Novi korisnik')

@section('content')

<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Dodaj korisnika</h2>

        <a href="{{ route('users.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="app-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="name">Ime i prezime *</label>
                    <input type="text" id="name" name="name" class="app-input" value="{{ old('name') }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="email">Email *</label>
                    <input type="email" id="email" name="email" class="app-input" value="{{ old('email') }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="password">Lozinka *</label>
                    <input type="password" id="password" name="password" class="app-input" required>
                </div>

                <div class="app-form-group flex items-end">
                    <div class="space-y-3">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                            <span>Admin</span>
                        </label>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <span>Aktivan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="flex gap-3">
                <button type="submit" class="app-button">Spremi</button>
                <a href="{{ route('users.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </div>
    </form>
</div>

@endsection