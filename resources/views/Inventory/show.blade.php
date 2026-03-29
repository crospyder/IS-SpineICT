@extends('layouts.app')

@section('title', 'Detalji uređaja')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">
                {{ $item->hostname ?? $item->device_name ?? 'Uređaj' }}
            </h2>
            <div class="text-sm app-muted mt-1">
                Partner: {{ $partner->name ?? '—' }}
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('inventory.edit', $item->id) }}" class="app-button-secondary">
                Uredi
            </a>

            <a href="{{ route('inventory.index') }}" class="app-button-secondary">
                Natrag
            </a>

            <form method="POST" action="{{ route('inventory.destroy', $item->id) }}" onsubmit="return confirm('Obrisati uređaj?')">
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium border border-red-500/30 text-red-300 hover:bg-red-500/10">
                    Obriši
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="app-card p-6">
                <h3 class="text-base font-semibold mb-4">Osnovni podaci</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Partner</div>
                        <div>{{ $partner->name ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Naziv uređaja</div>
                        <div>{{ $item->hostname ?? $item->device_name ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Identifikator uređaja</div>
                        <div class="break-all">{{ $item->agent_device_id ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Serijski broj</div>
                        <div>{{ $item->serial_number ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Proizvođač</div>
                        <div>{{ $item->manufacturer ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Model</div>
                        <div>{{ $item->model ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="app-card p-6">
                <h3 class="text-base font-semibold mb-4">Sustav i mreža</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Operacijski sustav</div>
                        <div>{{ trim(($item->os_name ?? '') . ' ' . ($item->os_version ?? '')) ?: '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">IP adresa</div>
                        <div>{{ $item->primary_ipv4 ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">MAC adresa</div>
                        <div>{{ $item->primary_mac ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Način inventure</div>
                        <div>{{ $item->inventory_mode ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Zadnje viđeno</div>
                        <div>{{ $item->last_seen_at ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="app-muted text-xs uppercase tracking-wider mb-1">Ažurirano</div>
                        <div>{{ $item->updated_at ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="app-card p-6">
                <h3 class="text-base font-semibold mb-4">Instalirani softver</h3>

                @if(count($software))
                    <div class="overflow-hidden">
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th>Naziv</th>
                                    <th>Verzija</th>
                                    <th>Izdavač</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($software as $row)
                                    <tr class="app-row">
                                        <td>{{ $row->name ?? '—' }}</td>
                                        <td>{{ $row->version ?? '—' }}</td>
                                        <td>{{ $row->publisher ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="app-muted">Nema evidentiranog softvera za ovaj uređaj.</div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="app-card p-6">
                <h3 class="text-base font-semibold mb-4">Sažetak</h3>

                <div class="space-y-3">
                    <div class="flex justify-between gap-4">
                        <span class="app-muted">ID</span>
                        <span>{{ $item->id }}</span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="app-muted">Interni uređaj</span>
                        <span>
                            @if(isset($item->is_internal))
                                {{ $item->is_internal ? 'Da' : 'Ne' }}
                            @else
                                —
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="app-muted">Inventura uključena</span>
                        <span>
                            @if(isset($item->inventory_enabled))
                                {{ $item->inventory_enabled ? 'Da' : 'Ne' }}
                            @else
                                —
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="app-muted">Kreirano</span>
                        <span>{{ $item->created_at ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="app-card p-6">
                <h3 class="text-base font-semibold mb-4">Povijest skeniranja</h3>

                @if(count($scans))
                    <div class="space-y-4">
                        @foreach($scans as $scan)
                            <div class="rounded-lg border border-white/10 p-4 bg-white/5">
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach((array) $scan as $key => $value)
                                        <div>
                                            <div class="app-muted text-xs uppercase tracking-wider mb-1">
                                                {{ str_replace('_', ' ', $key) }}
                                            </div>
                                            <div>
                                                {{ $value !== null && $value !== '' ? $value : '—' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="app-muted">Nema scan zapisa za ovaj uređaj.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection