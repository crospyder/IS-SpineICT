@extends('layouts.app')

@section('title', 'Nova obveza')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Dodaj obvezu</h2>

        <a href="{{ route('obligations.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form action="{{ route('obligations.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="app-form-group">
                    <div class="flex items-center justify-between mb-2">
                        <label class="app-label mb-0" for="partner_id">Partner *</label>

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

                <div class="app-form-group">
                    <div class="flex items-center justify-between mb-2">
                        <label class="app-label mb-0" for="partner_service_id">Usluga</label>

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

                <div class="app-form-group md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="app-label mb-0" for="partner_contact_id">Kontakt</label>

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

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="title">Naslov *</label>
                    <input type="text" id="title" name="title" class="app-input" value="{{ old('title') }}" required>
                </div>

                <div class="app-form-group md:col-span-2">
                    <label class="app-label" for="description">Opis</label>
                    <textarea id="description" name="description" rows="4" class="app-textarea">{{ old('description') }}</textarea>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="status">Status *</label>
                    <select id="status" name="status" class="app-select" required>
                        <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>U tijeku</option>
                        <option value="waiting" {{ old('status') === 'waiting' ? 'selected' : '' }}>Na čekanju</option>
                        <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Završeno</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="priority">Prioritet *</label>
                    <select id="priority" name="priority" class="app-select" required>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Nizak</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normalan</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Visok</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Hitno</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="due_date">Rok</label>
                    <input type="date" id="due_date" name="due_date" class="app-input" value="{{ old('due_date') }}">
                </div>

                <div class="app-form-group flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="is_recurring" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                        <span>Ponavljajuća obveza</span>
                    </label>
                </div>
            </div>

            <div id="recurringFields" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4" style="{{ old('is_recurring') ? '' : 'display:none;' }}">
                <div class="app-form-group">
                    <label class="app-label" for="recurrence_type">Ponavljanje</label>
                    <select id="recurrence_type" name="recurrence_type" class="app-select">
                        <option value="">-- odaberi --</option>
                        <option value="daily" {{ old('recurrence_type') === 'daily' ? 'selected' : '' }}>Dnevno</option>
                        <option value="weekly" {{ old('recurrence_type') === 'weekly' ? 'selected' : '' }}>Tjedno</option>
                        <option value="monthly" {{ old('recurrence_type') === 'monthly' ? 'selected' : '' }}>Mjesečno</option>
                        <option value="yearly" {{ old('recurrence_type') === 'yearly' ? 'selected' : '' }}>Godišnje</option>
                    </select>
                </div>

                <div class="app-form-group">
                    <label class="app-label" for="remind_days_before">Podsjetnik prije roka</label>
                    <select id="remind_days_before" name="remind_days_before" class="app-select">
                        <option value="">-- bez podsjetnika --</option>
                        <option value="0" {{ old('remind_days_before') === '0' ? 'selected' : '' }}>Na dan roka</option>
                        <option value="1" {{ old('remind_days_before') === '1' ? 'selected' : '' }}>1 dan prije</option>
                        <option value="3" {{ old('remind_days_before') === '3' ? 'selected' : '' }}>3 dana prije</option>
                        <option value="7" {{ old('remind_days_before') === '7' ? 'selected' : '' }}>7 dana prije</option>
                        <option value="14" {{ old('remind_days_before') === '14' ? 'selected' : '' }}>14 dana prije</option>
                        <option value="30" {{ old('remind_days_before') === '30' ? 'selected' : '' }}>30 dana prije</option>
                    </select>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 app-badge badge-overdue">
                    Provjeri unesene podatke.
                </div>
            @endif

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">Spremi</button>
                <a href="{{ route('obligations.index') }}" class="app-button-secondary">Odustani</a>
            </div>
        </form>
    </div>
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

    function toggleRecurringFields() {
        recurringFields.style.display = recurringCheckbox.checked ? '' : 'none';
    }

    function updateServiceOptions() {
        const selectedPartnerId = partnerSelect.value;

        Array.from(serviceSelect.options).forEach(option => {
            if (option.value === '') {
                option.hidden = false;
                return;
            }

            option.hidden = selectedPartnerId
                ? option.dataset.partnerId !== selectedPartnerId
                : false;
        });

        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        if (selectedOption && selectedOption.hidden) {
            serviceSelect.value = '';
        }
    }

    function updateContactOptions() {
        const selectedPartnerId = partnerSelect.value;

        Array.from(contactSelect.options).forEach(option => {
            if (option.value === '') {
                option.hidden = false;
                return;
            }

            option.hidden = selectedPartnerId
                ? option.dataset.partnerId !== selectedPartnerId
                : false;
        });

        const selectedOption = contactSelect.options[contactSelect.selectedIndex];
        if (selectedOption && selectedOption.hidden) {
            contactSelect.value = '';
        }
    }

    function updateAddServiceLink() {
        const baseUrl = "{{ route('partner-services.create') }}";
        const selectedPartnerId = partnerSelect.value;
        const params = new URLSearchParams({
            return_to: window.location.pathname,
            return_partner_field: 'partner_id',
            return_service_field: 'partner_service_id'
        });

        if (selectedPartnerId) {
            params.set('partner_id', selectedPartnerId);
        }

        addServiceLink.href = `${baseUrl}?${params.toString()}`;
    }

    function updateAddContactLink() {
        const baseUrl = "{{ route('partner-contacts.create') }}";
        const selectedPartnerId = partnerSelect.value;
        const params = new URLSearchParams({
            return_to: window.location.pathname,
            return_partner_field: 'partner_id',
            return_contact_field: 'partner_contact_id'
        });

        if (selectedPartnerId) {
            params.set('partner_id', selectedPartnerId);
        }

        addContactLink.href = `${baseUrl}?${params.toString()}`;
    }

    recurringCheckbox.addEventListener('change', toggleRecurringFields);
    partnerSelect.addEventListener('change', function () {
        updateServiceOptions();
        updateContactOptions();
        updateAddServiceLink();
        updateAddContactLink();
    });

    toggleRecurringFields();
    updateServiceOptions();
    updateContactOptions();
    updateAddServiceLink();
    updateAddContactLink();
});
</script>

@endsection