@extends('layouts.app')

@section('title', $item ? 'Uredi uređaj' : 'Dodaj uređaj')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">
                {{ $item ? 'Uredi uređaj' : 'Dodaj uređaj' }}
            </h2>
            <div class="text-sm app-muted mt-1">
                Ručni unos i uređivanje inventarnog zapisa
            </div>
        </div>

        <a href="{{ route('inventory.index') }}" class="app-button-secondary">
            Natrag
        </a>
    </div>

    <div class="app-card p-6">
        <form method="POST" action="{{ $item ? route('inventory.update', $item->id) : route('inventory.store') }}">
            @csrf
            @if($item)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($columns as $column)
                    @php
                        $labelMap = [
                            'partner_id' => 'Partner',
                            'hostname' => 'Naziv uređaja / hostname',
                            'device_name' => 'Naziv uređaja',
                            'agent_device_id' => 'Identifikator uređaja',
                            'serial_number' => 'Serijski broj',
                            'manufacturer' => 'Proizvođač',
                            'model' => 'Model',
                            'os_name' => 'Operacijski sustav',
                            'os_version' => 'Verzija OS-a',
                            'primary_ipv4' => 'IP adresa',
                            'primary_mac' => 'MAC adresa',
                            'inventory_mode' => 'Način inventure',
                            'inventory_enabled' => 'Inventura uključena',
                            'is_internal' => 'Interni uređaj',
                            'inventory_partner_key' => 'Partner inventory ključ',
                            'last_seen_at' => 'Zadnje viđeno',
                        ];

                        $label = $labelMap[$column] ?? ucwords(str_replace('_', ' ', $column));
                    @endphp

                    <div class="app-form-group">
                        <label class="app-label" for="{{ $column }}">{{ $label }}</label>

                        @if($column === 'partner_id')
                            <select name="{{ $column }}" id="{{ $column }}" class="app-input">
                                <option value="">— Odaberi partnera —</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}" {{ (string) old($column, $item->{$column} ?? '') === (string) $partner->id ? 'selected' : '' }}>
                                        {{ $partner->name }}
                                    </option>
                                @endforeach
                            </select>

                        @elseif(in_array($column, ['inventory_enabled', 'is_internal']))
                            <select name="{{ $column }}" id="{{ $column }}" class="app-input">
                                <option value="0" {{ (string) old($column, $item->{$column} ?? '0') === '0' ? 'selected' : '' }}>Ne</option>
                                <option value="1" {{ (string) old($column, $item->{$column} ?? '0') === '1' ? 'selected' : '' }}>Da</option>
                            </select>

                        @else
                            <input
                                type="text"
                                name="{{ $column }}"
                                id="{{ $column }}"
                                class="app-input"
                                value="{{ old($column, $item->{$column} ?? '') }}"
                            >
                        @endif

                        @error($column)
                            <div class="text-sm text-red-400 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="app-button">
                    Spremi
                </button>

                <a href="{{ route('inventory.index') }}" class="app-button-secondary">
                    Odustani
                </a>
            </div>
        </form>
    </div>
</div>

@endsection