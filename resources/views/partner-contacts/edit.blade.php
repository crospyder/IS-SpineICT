@extends('layouts.app')

@section('title', 'Uredi kontakt')

@section('content')

<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">Uredi kontakt</h2>
            <div class="text-sm app-muted mt-1">
                {{ trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'Kontakt' }}
            </div>
        </div>

        <a href="{{ route('partners.show', $item->partner_id) }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form action="{{ route('partner-contacts.update', $item) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="partner_id">Partner *</label>
                    <select id="partner_id" name="partner_id" class="app-select" required>
                        <option value="">-- odaberi --</option>
                        @foreach($partners as $partner)
                            <option
                                value="{{ $partner->id }}"
                                {{ (string) old('partner_id', $item->partner_id) === (string) $partner->id ? 'selected' : '' }}
                            >
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="first_name">Ime *</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="app-input"
                        value="{{ old('first_name', $item->first_name) }}"
                        required
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="last_name">Prezime</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="app-input"
                        value="{{ old('last_name', $item->last_name) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="app-input"
                        value="{{ old('email', $item->email) }}"
                    >
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="phone">Telefon</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="app-input"
                        value="{{ old('phone', $item->phone) }}"
                    >
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="job_title">Pozicija</label>
                    <input
                        type="text"
                        id="job_title"
                        name="job_title"
                        class="app-input"
                        value="{{ old('job_title', $item->job_title) }}"
                    >
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="notes">Bilješke</label>
                    <textarea id="notes" name="notes" rows="4" class="app-textarea">{{ old('notes', $item->notes) }}</textarea>
                </div>

                <div class="app-form-group">
                    <label class="inline-flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="is_primary"
                            value="1"
                            {{ old('is_primary', $item->is_primary) ? 'checked' : '' }}
                        >
                        <span>Primarni kontakt</span>
                    </label>
                </div>

                <div class="app-form-group">
                    <label class="inline-flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                        >
                        <span>Aktivan kontakt</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">Spremi izmjene</button>
                <a href="{{ route('partners.show', $item->partner_id) }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
</div>

@endsection