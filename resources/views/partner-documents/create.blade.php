@extends('layouts.app')

@section('title', 'Novi dokument')

@section('content')

<div class="max-w-4xl space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">Dodaj dokument</h2>
            <div class="text-sm app-muted mt-1">
                Partner: {{ $partner->name }}
            </div>
        </div>

        <a href="{{ route('partners.show', $partner) }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('partner-documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <input type="hidden" name="partner_id" value="{{ $partner->id }}">

        <div class="app-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Naslov *</label>
                    <input type="text" name="title" class="app-input" value="{{ old('title') }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Kategorija *</label>
                    <select name="category" class="app-select" required>
                        <option value="">-- odaberi --</option>
                        <option value="ugovor" @selected(old('category') === 'ugovor')>Ugovor</option>
                        <option value="aneks" @selected(old('category') === 'aneks')>Aneks</option>
                        <option value="ponuda" @selected(old('category') === 'ponuda')>Ponuda</option>
                        <option value="racun" @selected(old('category') === 'racun')>Račun</option>
                        <option value="licenca" @selected(old('category') === 'licenca')>Licenca</option>
                        <option value="dokumentacija" @selected(old('category') === 'dokumentacija')>Dokumentacija</option>
                        <option value="ostalo" @selected(old('category') === 'ostalo')>Ostalo</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Datum dokumenta</label>
                    <input type="date" name="document_date" class="app-input" value="{{ old('document_date') }}">
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Datoteka *</label>
                    <input type="file" name="file" class="app-input" required>
                    <div class="text-xs app-muted mt-2">
                        Preporučeno: PDF. Maksimalna veličina: 20 MB.
                    </div>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Bilješka</label>
                    <textarea name="notes" rows="4" class="app-textarea">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="flex gap-3">
                <button type="submit" class="app-button">Spremi dokument</button>
                <a href="{{ route('partners.show', $partner) }}" class="app-button-secondary">Odustani</a>
            </div>
        </div>
    </form>
</div>

@endsection