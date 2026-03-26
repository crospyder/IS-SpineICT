@extends('layouts.app')

@section('title', 'Uredi obvezu')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Uredi obvezu</h2>

        <a href="{{ route('obligations.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form action="{{ route('obligations.update', $obligation) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <label class="app-label" for="partner_id">Partner *</label>
                    <select id="partner_id" name="partner_id" class="app-select" required>
                        <option value="">-- odaberi --</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ old('partner_id', $obligation->partner_id) == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="partner_service_id">Usluga</label>
                    <select id="partner_service_id" name="partner_service_id" class="app-select">
                        <option value="">-- bez usluge --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('partner_service_id', $obligation->partner_service_id) == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="title">Naslov *</label>
                    <input type="text" id="title" name="title" class="app-input" value="{{ old('title', $obligation->title) }}" required>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="description">Opis</label>
                    <textarea id="description" name="description" rows="4" class="app-textarea">{{ old('description', $obligation->description) }}</textarea>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="status">Status *</label>
                    <input type="text" id="status" name="status" class="app-input" value="{{ old('status', $obligation->status) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="priority">Prioritet *</label>
                    <input type="text" id="priority" name="priority" class="app-input" value="{{ old('priority', $obligation->priority) }}" required>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="due_date">Rok</label>
                    <input type="date" id="due_date" name="due_date" class="app-input" value="{{ old('due_date', optional($obligation->due_date)->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">Spremi izmjene</button>
                <a href="{{ route('obligations.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
</div>

@endsection