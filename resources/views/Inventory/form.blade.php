@extends('layouts.app')

@section('title', $item ? 'Uredi inventory uređaj' : 'Dodaj inventory uređaj')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                {{ $item ? 'Uredi inventory uređaj' : 'Dodaj inventory uređaj' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Ručni unos i uređivanje inventory zapisa.
            </p>
        </div>

        <a href="{{ route('inventory.index') }}"
           class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Natrag
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <div class="font-medium mb-2">Provjeri unos:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $item ? route('inventory.update', $item->id) : route('inventory.store') }}">
            @csrf
            @if($item)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach($columns as $column)
                    <div>
                        <label for="{{ $column }}" class="mb-1 block text-sm font-medium text-slate-700">
                            {{ ucwords(str_replace('_', ' ', $column)) }}
                        </label>

                        @if($column === 'partner_id')
                            <select
                                id="{{ $column }}"
                                name="{{ $column }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            >
                                <option value="">— Odaberi partnera —</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}" @selected((string) old($column, $item->{$column} ?? '') === (string) $partner->id)>
                                        {{ $partner->name }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif(in_array($column, ['inventory_enabled', 'is_internal']))
                            <select
                                id="{{ $column }}"
                                name="{{ $column }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            >
                                <option value="0" @selected((string) old($column, $item->{$column} ?? '0') === '0')>Ne</option>
                                <option value="1" @selected((string) old($column, $item->{$column} ?? '0') === '1')>Da</option>
                            </select>
                        @else
                            <input
                                type="text"
                                id="{{ $column }}"
                                name="{{ $column }}"
                                value="{{ old($column, $item->{$column} ?? '') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            >
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Spremi
                </button>

                <a href="{{ route('inventory.index') }}"
                   class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Odustani
                </a>
            </div>
        </form>
    </div>
</div>
@endsection