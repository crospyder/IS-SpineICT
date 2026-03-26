@extends('layouts.app')

@section('title', 'Detalji usluge')

@section('content')

<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Detalji usluge</h2>

        <div class="flex gap-2">
            <a href="{{ route('partner-services.edit', $partnerService) }}" class="app-button">
                Uredi
            </a>

            <a href="{{ route('partner-services.index') }}" class="app-button-secondary">
                Natrag
            </a>
        </div>
    </div>

    <div class="app-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <div class="app-muted text-sm mb-1">Naziv</div>
                <div class="font-medium">{{ $partnerService->name ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Partner</div>
                <div class="font-medium">{{ $partnerService->partner?->name ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Tip usluge</div>
                <div>{{ $partnerService->service_type ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Status</div>
                <div>{{ $partnerService->status ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Domena</div>
                <div>{{ $partnerService->domain_name ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Provider</div>
                <div>{{ $partnerService->provider ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Registrar</div>
                <div>{{ $partnerService->registrar ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Period obnove</div>
                <div>{{ $partnerService->renewal_period ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Početak</div>
                <div>{{ $partnerService->starts_on ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Istek</div>
                <div>
                    @if($partnerService->expires_on)
                        @php
                            $expires = \Carbon\Carbon::parse($partnerService->expires_on);
                        @endphp

                        @if($expires->isPast())
                            <span class="app-badge badge-overdue">{{ $expires->format('Y-m-d') }}</span>
                        @elseif($expires->lte(now()->addDays(30)))
                            <span class="app-badge badge-soon">{{ $expires->format('Y-m-d') }}</span>
                        @else
                            <span class="app-badge badge-ok">{{ $expires->format('Y-m-d') }}</span>
                        @endif
                    @else
                        -
                    @endif
                </div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Datum obnove</div>
                <div>{{ $partnerService->renewal_date ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Način obnove</div>
                <div>{{ $partnerService->renewal_method ?: '-' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Admin link</div>
                <div>
                    @if($partnerService->admin_link)
                        <a href="{{ $partnerService->admin_link }}" target="_blank" class="app-link">
                            {{ $partnerService->admin_link }}
                        </a>
                    @else
                        -
                    @endif
                </div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Auto renew</div>
                <div>{{ $partnerService->auto_renew ? 'Da' : 'Ne' }}</div>
            </div>

            <div>
                <div class="app-muted text-sm mb-1">Aktivna</div>
                <div>
                    @if($partnerService->is_active)
                        <span class="app-badge badge-ok">Da</span>
                    @else
                        <span class="app-badge badge-overdue">Ne</span>
                    @endif
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="app-muted text-sm mb-1">Bilješke</div>
                <div>{{ $partnerService->notes ?: '-' }}</div>
            </div>

        </div>
    </div>
</div>

@endsection