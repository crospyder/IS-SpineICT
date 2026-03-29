@extends('layouts.app')

@section('title', 'Korisnici')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">Korisnici</h2>
            <div class="text-sm app-muted mt-1">
                Administracija korisničkih računa
            </div>
        </div>

        <a href="{{ route('users.create') }}" class="app-button">
            Novi korisnik
        </a>
    </div>

    <div class="app-card p-4 mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="app-form-group md:col-span-2">
                <label class="app-label" for="q">Pretraga</label>
                <input
                    type="text"
                    name="q"
                    id="q"
                    class="app-input"
                    value="{{ request('q') }}"
                    placeholder="Ime ili email"
                >
            </div>

            <div class="app-form-group">
                <label class="app-label" for="active">Status</label>
                <select name="active" id="active" class="app-input">
                    <option value="">Svi</option>
                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktivni</option>
                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Neaktivni</option>
                </select>
            </div>

            <div class="app-form-group">
                <label class="app-label" for="admin">Tip</label>
                <select name="admin" id="admin" class="app-input">
                    <option value="">Svi</option>
                    <option value="1" {{ request('admin') === '1' ? 'selected' : '' }}>Admin</option>
                    <option value="0" {{ request('admin') === '0' ? 'selected' : '' }}>Korisnik</option>
                </select>
            </div>

            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="app-button">Filtriraj</button>
                <a href="{{ route('users.index') }}" class="app-button-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="app-card p-6">
        @if($users->count())
            <div class="overflow-hidden">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Ime</th>
                            <th>Email</th>
                            <th>Tip</th>
                            <th>Status</th>
                            <th class="text-right">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="app-row">
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>

                                <td>
                                    @if($user->is_admin)
                                        <span class="app-badge badge-soon">Admin</span>
                                    @else
                                        <span class="app-badge badge-ok">Korisnik</span>
                                    @endif
                                </td>

                                <td>
                                    @if($user->is_active)
                                        <span class="app-badge badge-ok">Aktivan</span>
                                    @else
                                        <span class="app-badge badge-overdue">Neaktivan</span>
                                    @endif
                                </td>

                                <td class="text-right">
                                    <a href="{{ route('users.edit', $user) }}" class="app-button-secondary">
                                        Uredi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="app-muted">Nema korisnika za prikaz.</div>
        @endif
    </div>
</div>

@endsection