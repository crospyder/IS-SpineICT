@extends('layouts.app')

@section('title', 'Inventory detalji')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                {{ $item->hostname ?? $item->device_name ?? 'Inventory uređaj' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Partner: {{ $partner->name ?? '—' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventory.edit', $item->id) }}"
               class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Uredi
            </a>

            <a href="{{ route('inventory.index') }}"
               class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Natrag
            </a>

            <form method="POST" action="{{ route('inventory.destroy', $item->id) }}" onsubmit="return confirm('Obrisati uređaj?')">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Obriši
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Osnovni podaci</h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach((array) $item as $key => $value)
                        <div class="rounded-lg border border-slate-200 p-4">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {{ str_replace('_', ' ', $key) }}
                            </div>
                            <div class="break-words text-sm text-slate-800">
                                {{ $value !== null && $value !== '' ? $value : '—' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Installed software</h2>

                @if(count($software))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    @foreach(array_keys((array) $software[0]) as $column)
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            {{ str_replace('_', ' ', $column) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($software as $row)
                                    <tr>
                                        @foreach((array) $row as $value)
                                            <td class="px-4 py-3 text-sm text-slate-700">
                                                {{ $value !== null && $value !== '' ? $value : '—' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Nema software zapisa.</p>
                @endif
            </div>
        </div>

        <div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Scan history</h2>

                @if(count($scans))
                    <div class="space-y-4">
                        @foreach($scans as $scan)
                            <div class="rounded-lg border border-slate-200 p-4">
                                @foreach((array) $scan as $key => $value)
                                    <div class="mb-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            {{ str_replace('_', ' ', $key) }}:
                                        </span>
                                        <span class="ml-1 text-sm text-slate-800">
                                            {{ $value !== null && $value !== '' ? $value : '—' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">Nema scan zapisa.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection