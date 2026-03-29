@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Inventory</h1>
            <p class="text-sm text-slate-500 mt-1">
                Globalni pregled ingestanih i ručno dodanih uređaja.
            </p>
        </div>

        <a href="{{ route('inventory.create') }}"
           class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            Dodaj uređaj
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label for="search" class="mb-1 block text-sm font-medium text-slate-700">Pretraga</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="hostname, serial, model, partner..."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                >
            </div>

            <div>
                <label for="partner_id" class="mb-1 block text-sm font-medium text-slate-700">Partner</label>
                <select
                    id="partner_id"
                    name="partner_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                >
                    <option value="">Svi partneri</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" @selected((string) request('partner_id') === (string) $partner->id)>
                            {{ $partner->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-2">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Filtriraj
                </button>

                <a href="{{ route('inventory.index') }}"
                   class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Partner</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Hostname</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Proizvođač / model</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Serijski broj</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Agent device ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Ažurirano</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $item->partner_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">
                                {{ $item->hostname ?? $item->device_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ trim(($item->manufacturer ?? '') . ' ' . ($item->model ?? '')) ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $item->serial_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 break-all">
                                {{ $item->agent_device_id ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $item->updated_at ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('inventory.show', $item->id) }}"
                                       class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Detalji
                                    </a>
                                    <a href="{{ route('inventory.edit', $item->id) }}"
                                       class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Uredi
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                Nema inventory zapisa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection