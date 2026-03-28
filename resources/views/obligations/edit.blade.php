@extends('layouts.app')

@section('title', 'Uredi obvezu')

@section('content')

<div class="max-w-5xl space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">Uredi obvezu</h2>
            <div class="text-sm app-muted mt-1">
                {{ $obligation->title }}
            </div>
        </div>

        <a href="{{ route('obligations.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('obligations.update', $obligation) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- OSNOVNI PODACI --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Osnovni podaci</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Partner *</label>
                    <select id="partner_id" name="partner_id" class="app-select" required>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}"
                                {{ old('partner_id', $obligation->partner_id) == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Usluga</label>
                    <select id="partner_service_id" name="partner_service_id" class="app-select">
                        <option value="">-- bez usluge --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}"
                                data-partner-id="{{ $service->partner_id }}"
                                {{ old('partner_service_id', $obligation->partner_service_id) == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Naslov *</label>
                    <input type="text" name="title" class="app-input"
                           value="{{ old('title', $obligation->title) }}" required>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Opis</label>
                    <textarea name="description" class="app-textarea">{{ old('description', $obligation->description) }}</textarea>
                </div>

            </div>
        </div>

        {{-- STATUS --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Status i rok</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Status</label>
                    <select name="status" class="app-select">
                        <option value="open" @selected(old('status', $obligation->status) === 'open')>Open</option>
                        <option value="in_progress" @selected(old('status', $obligation->status) === 'in_progress')>U tijeku</option>
                        <option value="waiting" @selected(old('status', $obligation->status) === 'waiting')>Na čekanju</option>
                        <option value="done" @selected(old('status', $obligation->status) === 'done')>Završeno</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Prioritet</label>
                    <select name="priority" class="app-select">
                        <option value="low" @selected(old('priority', $obligation->priority) === 'low')>Nizak</option>
                        <option value="normal" @selected(old('priority', $obligation->priority) === 'normal')>Normalan</option>
                        <option value="high" @selected(old('priority', $obligation->priority) === 'high')>Visok</option>
                        <option value="urgent" @selected(old('priority', $obligation->priority) === 'urgent')>Hitno</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Rok</label>
                    <input type="date" name="due_date" class="app-input"
                           value="{{ old('due_date', optional($obligation->due_date)->format('Y-m-d')) }}">
                </div>

            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('obligations.index') }}" class="app-link text-sm">Odustani</a>

            <button type="submit" class="app-button">
                Spremi izmjene
            </button>
        </div>

    </form>

</div>

@endsection