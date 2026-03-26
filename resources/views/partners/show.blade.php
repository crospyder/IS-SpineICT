@extends('layouts.app')

@section('title', 'Partner karton')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold">{{ $partner->name }}</h2>
            <div class="app-muted text-sm mt-1">
                Partner karton
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('partners.edit', $partner) }}" class="app-button">
                Uredi
            </a>

            <a href="{{ route('partners.index') }}" class="app-button-secondary">
                Natrag
            </a>
        </div>
    </div>

    {{-- GORNJI BLOK --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">

        <div class="app-card p-6 xl:col-span-2">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold">Osnovni podaci</h3>

                <div>
                    @if($partner->is_active)
                        <span class="app-badge badge-ok">Aktivan</span>
                    @else
                        <span class="app-badge badge-overdue">Neaktivan</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <div class="app-muted text-sm mb-1">Naziv</div>
                    <div class="font-medium">{{ $partner->name ?: '-' }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Pravni naziv</div>
                    <div>{{ $partner->legal_name ?: '-' }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">OIB</div>
                    <div>{{ $partner->oib ?: '-' }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Email</div>
                    <div>{{ $partner->email ?: '-' }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Telefon</div>
                    <div>{{ $partner->phone ?: '-' }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Website</div>
                    <div>
                        @if($partner->website)
                            <a href="{{ $partner->website }}" target="_blank" class="app-link">
                                {{ $partner->website }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="app-muted text-sm mb-1">Adresa</div>
                    <div>{{ $partner->address ?: '-' }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Grad</div>
                    <div>{{ $partner->city ?: '-' }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Poštanski broj / država</div>
                    <div>
                        {{ $partner->postal_code ?: '-' }}
                        @if($partner->country)
                            / {{ $partner->country }}
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="app-muted text-sm mb-1">Bilješke</div>
                    <div>{{ $partner->notes ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="app-card p-6">
            <h3 class="text-lg font-semibold mb-4">Sažetak</h3>

            <div class="space-y-4">
                <div>
                    <div class="app-muted text-sm mb-1">Ukupno usluga</div>
                    <div class="text-2xl font-semibold">{{ $partner->services->count() }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Aktivne usluge</div>
                    <div class="text-2xl font-semibold">
                        {{ $partner->services->where('is_active', true)->count() }}
                    </div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Ukupno obveza</div>
                    <div class="text-2xl font-semibold">{{ $partner->obligations->count() }}</div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Otvorene obveze</div>
                    <div class="text-2xl font-semibold">
                        {{ $partner->obligations->filter(fn($o) => !$o->isCompleted())->count() }}
                    </div>
                </div>

                <div>
                    <div class="app-muted text-sm mb-1">Obveze koje kasne</div>
                    <div class="text-2xl font-semibold">
                        {{ $partner->obligations->filter(fn($o) => $o->isOverdue())->count() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- USLUGE --}}
    <div class="app-card p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Usluge</h3>

            <a href="{{ route('partner-services.create') }}" class="app-button-secondary">
                Dodaj uslugu
            </a>
        </div>

        @if($partner->services->count())
            <div class="overflow-hidden">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Naziv</th>
                            <th>Tip</th>
                            <th>Domena</th>
                            <th>Status</th>
                            <th>Istek</th>
                            <th class="text-right">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->services as $service)
                            <tr class="app-row">
                                <td>
                                    <a href="{{ route('partner-services.show', $service) }}" class="app-link">
                                        {{ $service->name }}
                                    </a>
                                </td>

                                <td>{{ $service->service_type ?: '-' }}</td>

                                <td>{{ $service->domain_name ?: '-' }}</td>

                                <td>
                                    @if($service->is_active)
                                        <span class="app-badge badge-ok">Aktivna</span>
                                    @else
                                        <span class="app-badge badge-overdue">Neaktivna</span>
                                    @endif
                                </td>

                                <td>
                                    @if($service->expires_on)
                                        @if($service->days_remaining !== null && $service->days_remaining < 0)
                                            <span class="app-badge badge-overdue">
                                                {{ $service->expires_on_formatted }}
                                                ({{ abs($service->days_remaining) }} dana kasni)
                                            </span>
                                        @elseif($service->days_remaining !== null && $service->days_remaining <= 30)
                                            <span class="app-badge badge-soon">
                                                {{ $service->expires_on_formatted }}
                                                ({{ $service->days_remaining }} dana)
                                            </span>
                                        @else
                                            <span class="app-badge badge-ok">
                                                {{ $service->expires_on_formatted }}
                                                @if($service->days_remaining !== null)
                                                    ({{ $service->days_remaining }} dana)
                                                @endif
                                            </span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('partner-services.show', $service) }}" class="app-button-secondary">
                                            Otvori
                                        </a>
                                        <a href="{{ route('partner-services.edit', $service) }}" class="app-button-secondary">
                                            Uredi
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="app-muted">Nema evidentiranih usluga.</div>
        @endif
    </div>
    {{-- KONTAKTI --}}
    <div class="app-card p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Kontakti</h3>

            <a href="{{ route('partner-contacts.create', ['partner_id' => $partner->id]) }}"
               class="app-button-secondary">
                Dodaj kontakt
            </a>
        </div>

        @if($partner->contacts->count())
            <div class="overflow-hidden">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Ime</th>
                            <th>Pozicija</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Primarni</th>
                            <th class="text-right">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->contacts as $contact)
                            <tr class="app-row">
                                <td>{{ $contact->name }}</td>

                                <td>{{ $contact->position ?: '-' }}</td>

                                <td>
                                    @if($contact->email)
                                        <a href="mailto:{{ $contact->email }}" class="app-link">
                                            {{ $contact->email }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $contact->phone ?: '-' }}</td>

                                <td>
                                    @if($contact->is_primary)
                                        <span class="app-badge badge-ok">Da</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('partner-contacts.edit', $contact) }}"
                                           class="app-button-secondary">
                                            Uredi
                                        </a>

                                        <form method="POST"
                                              action="{{ route('partner-contacts.destroy', $contact) }}"
                                              onsubmit="return confirm('Obrisati kontakt?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="app-button-secondary">
                                                Obriši
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="app-muted">Nema kontakata.</div>
        @endif
    </div>
    {{-- CREDENTIALS --}}
<div class="app-card p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Pristupi</h3>

        <a href="{{ route('credentials.create', ['partner_id' => $partner->id]) }}"
           class="app-button-secondary">
            Dodaj pristup
        </a>
    </div>

    @if($partner->credentials->count())
        <table class="app-table">
            <thead>
                <tr>
                    <th>Naziv</th>
                    <th>Korisničko ime</th>
                    <th>Lozinka</th>
                    <th>URL</th>
                    <th class="text-right">Akcije</th>
                </tr>
            </thead>

            <tbody>
                @foreach($partner->credentials as $c)
                    <tr class="app-row">
                        <td>{{ $c->title }}</td>

                        <td>{{ $c->username ?: '-' }}</td>

                        <td>
    <div class="flex items-center gap-2 justify-start">
        <span id="credential-password-{{ $c->id }}" class="app-muted">********</span>

        <button type="button"
                class="app-button-secondary"
                onclick="revealCredentialPassword({{ $c->id }})">
            Prikaži
        </button>

        <button type="button"
                class="app-button-secondary"
                onclick="copyCredentialPassword({{ $c->id }})">
            Kopiraj
        </button>
    </div>
</td>

                        <td>
                            @if($c->url)
                                <a href="{{ $c->url }}" target="_blank" class="app-link">
                                    Otvori
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('credentials.edit', $c) }}"
                                   class="app-button-secondary">
                                    Uredi
                                </a>

                                <form method="POST"
                                      action="{{ route('credentials.destroy', $c) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button class="app-button-secondary">
                                        Obriši
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="app-muted">Nema pristupa.</div>
    @endif
</div>
    {{-- OBVEZE --}}
    <div class="app-card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Obveze</h3>

            <a href="{{ route('obligations.create') }}" class="app-button-secondary">
                Dodaj obvezu
            </a>
        </div>

        @if($partner->obligations->count())
            <div class="overflow-hidden">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Naslov</th>
                            <th>Usluga</th>
                            <th>Status</th>
                            <th>Prioritet</th>
                            <th>Rok</th>
                            <th class="text-right">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->obligations as $obligation)
                            <tr class="app-row">
                                <td>
                                    <a href="{{ route('obligations.show', $obligation) }}" class="app-link">
                                        {{ $obligation->title }}
                                    </a>
                                </td>

                                <td>{{ $obligation->partnerService?->name ?: '-' }}</td>

                                <td>
                                    @if($obligation->isCompleted())
                                        <span class="app-badge badge-ok">Završeno</span>
                                    @elseif($obligation->isOverdue())
                                        <span class="app-badge badge-overdue">Kasni</span>
                                    @elseif($obligation->isExpiringSoon())
                                        <span class="app-badge badge-soon">Uskoro</span>
                                    @else
                                        <span class="app-badge badge-ok">Aktivno</span>
                                    @endif
                                </td>

                                <td>{{ $obligation->priority ?: '-' }}</td>

                                <td>
                                    @if($obligation->due_date)
                                        <span class="app-badge
                                            @if($obligation->isOverdue()) badge-overdue
                                            @elseif($obligation->isExpiringSoon()) badge-soon
                                            @else badge-ok
                                            @endif">
                                            {{ $obligation->due_date_formatted }}

                                            @if($obligation->days_remaining !== null)
                                                @if($obligation->days_remaining >= 0)
                                                    ({{ $obligation->days_remaining }} dana)
                                                @else
                                                    ({{ abs($obligation->days_remaining) }} dana kasni)
                                                @endif
                                            @endif
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(!$obligation->isCompleted())
                                            <form action="{{ route('obligations.complete', $obligation) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Označiti obvezu kao završenu?');">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="app-button-secondary">
                                                    Završi
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('obligations.show', $obligation) }}" class="app-button-secondary">
                                            Otvori
                                        </a>

                                        <a href="{{ route('obligations.edit', $obligation) }}" class="app-button-secondary">
                                            Uredi
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="app-muted">Nema evidentiranih obveza.</div>
        @endif
    </div>
</div>
<script>
async function fetchCredentialPassword(id) {
    const response = await fetch(`/credentials/${id}/reveal`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    if (!response.ok) {
        throw new Error('Ne mogu dohvatiti lozinku.');
    }

    return await response.json();
}

async function revealCredentialPassword(id) {
    const target = document.getElementById(`credential-password-${id}`);

    try {
        const data = await fetchCredentialPassword(id);
        target.textContent = data.password ?? '—';

        setTimeout(() => {
            target.textContent = '********';
        }, 15000);
    } catch (error) {
        alert('Greška pri dohvaćanju lozinke.');
    }
}

async function copyCredentialPassword(id) {
    try {
        const data = await fetchCredentialPassword(id);
        await navigator.clipboard.writeText(data.password ?? '');

        const target = document.getElementById(`credential-password-${id}`);
        const original = target.textContent;

        target.textContent = 'Kopirano';
        setTimeout(() => {
            target.textContent = original === 'Kopirano' ? '********' : original;
        }, 1500);
    } catch (error) {
        alert('Greška pri kopiranju lozinke.');
    }
}
</script>

@endsection