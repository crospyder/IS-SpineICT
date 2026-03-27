@extends('layouts.app')

@section('title', 'Novi kontakt')

@section('content')

<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Dodaj kontakt</h2>

        <a href="{{ $returnTo ?: route('partners.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form action="{{ route('partner-contacts.store') }}" method="POST">
            @csrf

            <input type="hidden" name="return_to" value="{{ $returnTo }}">
            <input type="hidden" name="return_partner_field" value="{{ $returnPartnerField }}">
            <input type="hidden" name="return_contact_field" value="{{ $returnContactField }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="partner_id">Partner *</label>
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

                <div class="app-form-group">
                    <label class="app-label" for="first_name">Ime *</label>
                    <input type="text" id="first_name" name="first_name" class="app-input" value="{{ old('first_name') }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="last_name">Prezime</label>
                    <input type="text" id="last_name" name="last_name" class="app-input" value="{{ old('last_name') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="app-input" value="{{ old('email') }}">
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" class="app-input" value="{{ old('phone') }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="job_title">Pozicija</label>
                    <input type="text" id="job_title" name="job_title" class="app-input" value="{{ old('job_title') }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="notes">Bilješke</label>
                    <textarea id="notes" name="notes" rows="4" class="app-textarea">{{ old('notes') }}</textarea>
                </div>

                <div class="app-form-group">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_primary" value="1" {{ old('is_primary') ? 'checked' : '' }}>
                        <span>Primarni kontakt</span>
                    </label>
                </div>

                <div class="app-form-group">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <span>Aktivan kontakt</span>
                    </label>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 app-badge badge-overdue">
                    Provjeri unesene podatke.
                </div>
            @endif

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">Spremi</button>
                <a href="{{ $returnTo ?: route('partners.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
</div>

@endsection