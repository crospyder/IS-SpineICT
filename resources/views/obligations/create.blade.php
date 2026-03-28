@extends('layouts.app')

@section('title', 'Nova obveza')

@section('content')

<div class="max-w-5xl space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">Nova obveza</h2>
            <div class="text-sm app-muted mt-1">
                Operativni zadatak ili podsjetnik
            </div>
        </div>

        <a href="{{ route('obligations.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <form action="{{ route('obligations.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- OSNOVNI PODACI --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Osnovni podaci</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- PARTNER --}}
                <div class="app-form-group">
                    <div class="flex items-center justify-between mb-2">
                        <label class="app-label mb-0">Partner *</label>

                        <a
                            href="{{ route('partners.create', ['return_to' => url()->current(), 'return_partner_field' => 'partner_id']) }}"
                            class="app-link text-sm"
                        >
                            + Novi partner
                        </a>
                    </div>

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

                {{-- USLUGA --}}
                <div class="app-form-group">
                    <div class="flex items-center justify-between mb-2">
                        <label class="app-label mb-0">Usluga</label>

                        <a
                            href="{{ route('partner-services.create', [
                                'return_to' => url()->current(),
                                'return_partner_field' => 'partner_id',
                                'return_service_field' => 'partner_service_id',
                                'partner_id' => request('partner_id'),
                            ]) }}"
                            id="add-service-link"
                            class="app-link text-sm"
                        >
                            + Nova usluga
                        </a>
                    </div>

                    <select id="partner_service_id" name="partner_service_id" class="app-select">
                        <option value="">-- bez usluge --</option>
                        @foreach($services as $service)
                            <option
                                value="{{ $service->id }}"
                                data-partner-id="{{ $service->partner_id }}"
                                {{ (string) old('partner_service_id', request('partner_service_id')) === (string) $service->id ? 'selected' : '' }}
                            >
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- KONTAKT --}}
                <div class="app-form-group md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="app-label mb-0">Kontakt</label>

                        <a
                            href="{{ route('partner-contacts.create', [
                                'return_to' => url()->current(),
                                'return_partner_field' => 'partner_id',
                                'return_contact_field' => 'partner_contact_id',
                                'partner_id' => request('partner_id'),
                            ]) }}"
                            id="add-contact-link"
                            class="app-link text-sm"
                        >
                            + Novi kontakt
                        </a>
                    </div>

                    <select id="partner_contact_id" name="partner_contact_id" class="app-select">
                        <option value="">-- bez kontakta --</option>
                        @foreach($contacts as $contact)
                            <option
                                value="{{ $contact->id }}"
                                data-partner-id="{{ $contact->partner_id }}"
                                {{ (string) old('partner_contact_id', request('partner_contact_id')) === (string) $contact->id ? 'selected' : '' }}
                            >
                                {{ $contact->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- NASLOV --}}
                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Naslov *</label>
                    <input type="text" name="title" class="app-input" value="{{ old('title') }}" required>
                </div>

                {{-- OPIS --}}
                <div class="app-form-group md:col-span-2">
                    <label class="app-label">Opis</label>
                    <textarea name="description" rows="4" class="app-textarea">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- STATUS I ROK --}}
        <div class="app-card p-6 space-y-4">
            <div class="text-sm font-semibold">Status i rok</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="app-form-group">
                    <label class="app-label">Status *</label>
                    <select name="status" class="app-select" required>
                        <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>U tijeku</option>
                        <option value="waiting" {{ old('status') === 'waiting' ? 'selected' : '' }}>Na čekanju</option>
                        <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Završeno</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Prioritet *</label>
                    <select name="priority" class="app-select" required>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Nizak</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normalan</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Visok</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Hitno</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Rok</label>
                    <input type="date" name="due_date" class="app-input" value="{{ old('due_date') }}">
                </div>

            </div>
        </div>

        {{-- RECURRING --}}
        <div class="app-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="text-sm font-semibold">Ponavljanje</div>

                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" id="is_recurring" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                    Ponavljajuća obveza
                </label>
            </div>

            <div id="recurringFields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ old('is_recurring') ? '' : 'hidden' }}">

                <div class="app-form-group">
                    <label class="app-label">Ponavljanje</label>
                    <select name="recurrence_type" class="app-select">
                        <option value="">-- odaberi --</option>
                        <option value="daily" {{ old('recurrence_type') === 'daily' ? 'selected' : '' }}>Dnevno</option>
                        <option value="weekly" {{ old('recurrence_type') === 'weekly' ? 'selected' : '' }}>Tjedno</option>
                        <option value="monthly" {{ old('recurrence_type') === 'monthly' ? 'selected' : '' }}>Mjesečno</option>
                        <option value="yearly" {{ old('recurrence_type') === 'yearly' ? 'selected' : '' }}>Godišnje</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label">Podsjetnik</label>
                    <select name="remind_days_before" class="app-select">
                        <option value="">-- bez podsjetnika --</option>
                        <option value="0" {{ old('remind_days_before') === '0' ? 'selected' : '' }}>Na dan</option>
                        <option value="1" {{ old('remind_days_before') === '1' ? 'selected' : '' }}>1 dan</option>
                        <option value="3" {{ old('remind_days_before') === '3' ? 'selected' : '' }}>3 dana</option>
                        <option value="7" {{ old('remind_days_before') === '7' ? 'selected' : '' }}>7 dana</option>
                        <option value="14" {{ old('remind_days_before') === '14' ? 'selected' : '' }}>14 dana</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- ERRORS --}}
        @if ($errors->any())
            <div class="app-card p-4 border border-red-500/30 bg-red-500/10">
                <div class="font-medium mb-2">Greška:</div>
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>— {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ACTIONS --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('obligations.index') }}" class="app-link text-sm">
                Odustani
            </a>

            <button type="submit" class="app-button">
                Spremi obvezu
            </button>
        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const recurringCheckbox = document.getElementById('is_recurring');
    const recurringFields = document.getElementById('recurringFields');

    const partnerSelect = document.getElementById('partner_id');
    const serviceSelect = document.getElementById('partner_service_id');
    const contactSelect = document.getElementById('partner_contact_id');

    const addServiceLink = document.getElementById('add-service-link');
    const addContactLink = document.getElementById('add-contact-link');

    function toggleRecurring() {
        recurringFields.classList.toggle('hidden', !recurringCheckbox.checked);
    }

    function filterOptions(select) {
        const partnerId = partnerSelect.value;

        Array.from(select.options).forEach(opt => {
            if (!opt.value) return;
            opt.hidden = partnerId ? opt.dataset.partnerId !== partnerId : false;
        });

        const selected = select.options[select.selectedIndex];
        if (selected && selected.hidden) {
            select.value = '';
        }
    }

    function updateLinks() {
        const partnerId = partnerSelect.value;

        if (partnerId) {
            addServiceLink.href = "{{ route('partner-services.create') }}" + '?partner_id=' + partnerId;
            addContactLink.href = "{{ route('partner-contacts.create') }}" + '?partner_id=' + partnerId;
        }
    }

    recurringCheckbox.addEventListener('change', toggleRecurring);

    partnerSelect.addEventListener('change', () => {
        filterOptions(serviceSelect);
        filterOptions(contactSelect);
        updateLinks();
    });

    toggleRecurring();
    filterOptions(serviceSelect);
    filterOptions(contactSelect);
    updateLinks();
});
</script>

@endsection