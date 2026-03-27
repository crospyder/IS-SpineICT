@extends('layouts.app')

@section('title', 'Uredi kalkulaciju')

@section('content')

<div class="max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold">{{ $item->title }}</h2>
            <div class="text-sm app-muted mt-1">
                Osnovni dokument / priprema za items UI
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('procurements.index') }}" class="app-button-secondary">
                Natrag
            </a>

            <form method="POST" action="{{ route('procurements.destroy', $item) }}" onsubmit="return confirm('Obrisati kalkulaciju?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="app-button-secondary">Obriši</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="app-card p-4 mb-4">
            <div class="text-sm">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="app-card p-4 mb-4">
            <div class="font-medium mb-2">Provjeri unos:</div>
            <ul class="text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>— {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $itemsCollection = $item->items;
        $costsCollection = $item->costs;

        $itemsPurchaseNet = $itemsCollection->sum(fn ($row) => $row->purchase_net_total);
        $itemsSaleNet = $itemsCollection->sum(fn ($row) => $row->sale_net_total);
        $itemsSaleGross = $itemsCollection->sum(fn ($row) => $row->sale_gross_total);
        $itemsProfitNet = $itemsCollection->sum(fn ($row) => $row->profit_net);
        $itemsPurchaseVat = $itemsCollection->sum(fn ($row) => $row->purchase_vat_total);
        $itemsSaleVat = $itemsCollection->sum(fn ($row) => $row->sale_vat_total);

        $fieldCosts = $costsCollection->where('cost_type', 'field');
        $otherCosts = $costsCollection->where('cost_type', '!=', 'field');

        $costsIncludedInMargin = $costsCollection->where('include_in_margin', true);
        $costsIncludedInOffer = $costsCollection->where('include_in_offer', true);

        $fieldCostNet = $fieldCosts->sum(fn ($row) => $row->total_net);
        $otherCostNet = $otherCosts->sum(fn ($row) => $row->total_net);
        $allCostNet = $costsCollection->sum(fn ($row) => $row->total_net);
        $allCostVat = $costsCollection->sum(fn ($row) => $row->vat_total);

        $marginCostNet = $costsIncludedInMargin->sum(fn ($row) => $row->total_net);
        $offerCostNet = $costsIncludedInOffer->sum(fn ($row) => $row->total_net);
        $offerCostVat = $costsIncludedInOffer->sum(fn ($row) => $row->vat_total);

        $finalPurchaseNet = $itemsPurchaseNet + $marginCostNet;
        $finalSaleNet = $itemsSaleNet + $offerCostNet;
        $finalSaleGross = $itemsSaleGross + $offerCostNet + $offerCostVat;
        $finalProfitNet = $itemsSaleNet - ($itemsPurchaseNet + $marginCostNet);
        $vatEffect = ($itemsSaleVat + $offerCostVat) - ($itemsPurchaseVat + $allCostVat);

        $itemTypeLabels = [
            'goods' => 'Roba',
            'service' => 'Usluga',
            'software' => 'Softver',
            'hardware' => 'Hardver',
            'subscription' => 'Pretplata',
            'other' => 'Ostalo',
        ];

        $costTypeLabels = [
            'field' => 'Terenski trošak',
            'logistics' => 'Logistika',
            'shipping' => 'Dostava',
            'customs' => 'Carina',
            'travel' => 'Putni trošak',
            'service' => 'Usluga',
            'other' => 'Ostalo',
        ];
    @endphp

    <div class="space-y-6">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <div class="app-card p-6">
                    <h3 class="font-semibold mb-4">Osnovni podaci</h3>

                    <form method="POST" action="{{ route('procurements.update', $item) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Partner *</label>
                                <select name="partner_id" class="app-select" required>
                                    <option value="">-- odaberi partnera --</option>
                                    @foreach($partners as $partner)
                                        <option value="{{ $partner->id }}" @selected(old('partner_id', $item->partner_id) == $partner->id)>
                                            {{ $partner->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Naziv kalkulacije *</label>
                                <input name="title" class="app-input" value="{{ old('title', $item->title) }}" required>
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Referentni broj</label>
                                <input name="reference_no" class="app-input" value="{{ old('reference_no', $item->reference_no) }}">
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Status *</label>
                                <select name="status" class="app-select" required>
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $item->status) === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Datum ponude</label>
                                <input
                                    type="date"
                                    name="offer_date"
                                    class="app-input"
                                    value="{{ old('offer_date', optional($item->offer_date)->format('Y-m-d')) }}"
                                >
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Vrijedi do</label>
                                <input
                                    type="date"
                                    name="valid_until"
                                    class="app-input"
                                    value="{{ old('valid_until', optional($item->valid_until)->format('Y-m-d')) }}"
                                >
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Prodajna valuta *</label>
                                <select name="default_sale_currency" class="app-select" required>
                                    <option value="EUR" @selected(old('default_sale_currency', $item->default_sale_currency) === 'EUR')>EUR</option>
                                    <option value="USD" @selected(old('default_sale_currency', $item->default_sale_currency) === 'USD')>USD</option>
                                </select>
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Nabavna valuta *</label>
                                <select name="default_purchase_currency" class="app-select" required>
                                    <option value="EUR" @selected(old('default_purchase_currency', $item->default_purchase_currency) === 'EUR')>EUR</option>
                                    <option value="USD" @selected(old('default_purchase_currency', $item->default_purchase_currency) === 'USD')>USD</option>
                                </select>
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">FX EUR → USD *</label>
                                <input
                                    type="number"
                                    step="0.000001"
                                    min="0.000001"
                                    name="fx_eur_to_usd"
                                    class="app-input"
                                    value="{{ old('fx_eur_to_usd', number_format((float) $item->fx_eur_to_usd, 6, '.', '')) }}"
                                    required
                                >
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">FX USD → EUR *</label>
                                <input
                                    type="number"
                                    step="0.000001"
                                    min="0.000001"
                                    name="fx_usd_to_eur"
                                    class="app-input"
                                    value="{{ old('fx_usd_to_eur', number_format((float) $item->fx_usd_to_eur, 6, '.', '')) }}"
                                    required
                                >
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Default PDV (%) *</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    name="vat_rate"
                                    class="app-input"
                                    value="{{ old('vat_rate', number_format((float) $item->vat_rate, 2, '.', '')) }}"
                                    required
                                >
                            </div>

                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Napomena</label>
                                <textarea name="notes" rows="4" class="app-input">{{ old('notes', $item->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button class="app-button" type="submit">Spremi promjene</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="app-card p-6">
                    <h3 class="font-semibold mb-4">Sažetak</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="app-muted">Partner</div>
                            <div>{{ $item->partner->name ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Status</div>
                            <div>{{ $statuses[$item->status] ?? $item->status }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Valute</div>
                            <div>{{ $item->default_purchase_currency }} → {{ $item->default_sale_currency }}</div>
                        </div>

                        <div>
                            <div class="app-muted">FX snapshot</div>
                            <div>EUR/USD: {{ number_format((float) $item->fx_eur_to_usd, 4, ',', '.') }}</div>
                            <div>USD/EUR: {{ number_format((float) $item->fx_usd_to_eur, 4, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Datum kreiranja</div>
                            <div>{{ $item->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="app-card p-6">
                    <h3 class="font-semibold mb-4">Totals</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="app-muted">Stavke nabavna neto</div>
                            <div class="font-semibold">{{ number_format((float) $itemsPurchaseNet, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Trošak terena</div>
                            <div class="font-semibold">{{ number_format((float) $fieldCostNet, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Ostali troškovi</div>
                            <div class="font-semibold">{{ number_format((float) $otherCostNet, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Ukupno troškovi</div>
                            <div class="font-semibold">{{ number_format((float) $allCostNet, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Nabavna neto + marža troškovi</div>
                            <div class="font-semibold">{{ number_format((float) $finalPurchaseNet, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Prodajna neto</div>
                            <div class="font-semibold">{{ number_format((float) $finalSaleNet, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Bruto prodajna</div>
                            <div class="font-semibold">{{ number_format((float) $finalSaleGross, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">Marža neto</div>
                            <div class="font-semibold">{{ number_format((float) $finalProfitNet, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="app-muted">PDV efekt</div>
                            <div class="font-semibold">{{ number_format((float) $vatEffect, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold">Stavke kalkulacije</h3>
                    <div class="text-sm app-muted mt-1">
                        Kompaktni prikaz. Uređivanje se otvara samo za odabranu stavku.
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="app-table w-full">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Stavka</th>
                            <th>Tip</th>
                            <th>Dobavljač</th>
                            <th>Količina</th>
                            <th>Nabavna neto</th>
                            <th>Prodajna neto</th>
                            <th>Bruto prodajna</th>
                            <th>Marža</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($item->items->sortBy('sort_order') as $row)
                            <tr>
                                <td>{{ $row->sort_order }}</td>

                                <td>
                                    <div class="font-medium">{{ $row->name }}</div>

                                    @if($row->description)
                                        <div class="text-xs app-muted mt-1">
                                            {{ \Illuminate\Support\Str::limit($row->description, 70) }}
                                        </div>
                                    @endif

                                    @if($row->is_optional)
                                        <div class="text-xs app-muted mt-1">Opcionalno</div>
                                    @endif
                                </td>

                                <td>{{ $itemTypeLabels[$row->item_type] ?? $row->item_type }}</td>

                                <td>
                                    <div>{{ $row->supplier_name ?: '-' }}</div>
                                    <div class="text-xs app-muted">
                                        {{ $row->supplier_origin === 'foreign' ? 'Strani' : 'Domaći' }}
                                    </div>
                                </td>

                                <td>{{ number_format((float) $row->quantity, 2, ',', '.') }}</td>

                                <td>
                                    {{ number_format((float) $row->purchase_net_total, 2, ',', '.') }}
                                    {{ $row->purchase_currency }}
                                </td>

                                <td>
                                    {{ number_format((float) $row->sale_net_total, 2, ',', '.') }}
                                    {{ $row->sale_currency }}
                                </td>

                                <td>
                                    {{ number_format((float) $row->sale_gross_total, 2, ',', '.') }}
                                    {{ $row->sale_currency }}
                                </td>

                                <td>
                                    {{ number_format((float) $row->profit_net, 2, ',', '.') }}
                                    {{ $row->sale_currency }}
                                </td>

                                <td class="text-right whitespace-nowrap">
                                    <a
                                        href="{{ route('procurements.edit', ['procurement' => $item->id, 'edit_item' => $row->id]) }}#item-edit-{{ $row->id }}"
                                        class="app-link mr-3"
                                    >
                                        Uredi
                                    </a>

                                    <form method="POST" action="{{ route('procurements.items.destroy', [$item, $row]) }}" class="inline" onsubmit="return confirm('Obrisati stavku?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-link">Obriši</button>
                                    </form>
                                </td>
                            </tr>

                            @if((string) request('edit_item') === (string) $row->id)
                                <tr id="item-edit-{{ $row->id }}">
                                    <td colspan="10" class="p-0">
                                        <div class="border-t p-4 bg-black/5">
                                            <div class="font-medium mb-4">Uredi stavku</div>

                                            <form method="POST" action="{{ route('procurements.items.update', [$item, $row]) }}">
                                                @csrf
                                                @method('PUT')

                                                <input type="hidden" name="sort_order" value="{{ $row->sort_order }}">

                                                <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                                                    <div class="app-form-group md:col-span-2">
                                                        <label class="app-label">Naziv</label>
                                                        <input name="name" class="app-input" value="{{ $row->name }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Tip</label>
                                                        <select name="item_type" class="app-select" required>
                                                            @foreach(['goods','service','software','hardware','subscription','other'] as $type)
                                                                <option value="{{ $type }}" @selected($row->item_type === $type)>
                                                                    {{ $itemTypeLabels[$type] ?? $type }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Dobavljač</label>
                                                        <input name="supplier_name" class="app-input" value="{{ $row->supplier_name }}">
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Podrijetlo</label>
                                                        <select name="supplier_origin" class="app-select" required>
                                                            <option value="domestic" @selected($row->supplier_origin === 'domestic')>Domaći</option>
                                                            <option value="foreign" @selected($row->supplier_origin === 'foreign')>Strani</option>
                                                        </select>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Količina</label>
                                                        <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="{{ number_format((float) $row->quantity, 3, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Nabavna valuta</label>
                                                        <select name="purchase_currency" class="app-select" required>
                                                            <option value="EUR" @selected($row->purchase_currency === 'EUR')>EUR</option>
                                                            <option value="USD" @selected($row->purchase_currency === 'USD')>USD</option>
                                                        </select>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Prodajna valuta</label>
                                                        <select name="sale_currency" class="app-select" required>
                                                            <option value="EUR" @selected($row->sale_currency === 'EUR')>EUR</option>
                                                            <option value="USD" @selected($row->sale_currency === 'USD')>USD</option>
                                                        </select>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Status flag</label>
                                                        <input name="status_flag" class="app-input" value="{{ $row->status_flag }}">
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Nabavna / kom</label>
                                                        <input type="number" step="0.0001" min="0" name="purchase_net_unit" class="app-input" value="{{ number_format((float) $row->purchase_net_unit, 4, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Prodajna / kom</label>
                                                        <input type="number" step="0.0001" min="0" name="sale_net_unit" class="app-input" value="{{ number_format((float) $row->sale_net_unit, 4, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Nabavni PDV %</label>
                                                        <input type="number" step="0.01" min="0" max="100" name="purchase_vat_rate" class="app-input" value="{{ number_format((float) $row->purchase_vat_rate, 2, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Prodajni PDV %</label>
                                                        <input type="number" step="0.01" min="0" max="100" name="sale_vat_rate" class="app-input" value="{{ number_format((float) $row->sale_vat_rate, 2, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group md:col-span-6">
                                                        <label class="app-label">Opis</label>
                                                        <textarea name="description" rows="2" class="app-input">{{ $row->description }}</textarea>
                                                    </div>

                                                    <div class="app-form-group md:col-span-6">
                                                        <label class="app-label">Napomena</label>
                                                        <textarea name="notes" rows="2" class="app-input">{{ $row->notes }}</textarea>
                                                    </div>

                                                    <div class="app-form-group md:col-span-6">
                                                        <label class="inline-flex items-center gap-2">
                                                            <input type="checkbox" name="is_optional" value="1" @checked($row->is_optional)>
                                                            <span class="text-sm">Opcionalna stavka</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mt-4 flex gap-3">
                                                    <button type="submit" class="app-button-secondary">Spremi stavku</button>

                                                    <a href="{{ route('procurements.edit', $item) }}#items-nova" class="app-button-secondary">
                                                        Zatvori
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="10" class="app-muted">Nema stavki.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="items-nova" class="border-t mt-6 pt-6">
                <h4 class="font-semibold mb-4">Dodaj novu stavku</h4>

                <form method="POST" action="{{ route('procurements.items.store', $item) }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="app-form-group md:col-span-2">
                            <label class="app-label">Naziv *</label>
                            <input name="name" class="app-input" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Tip *</label>
                            <select name="item_type" class="app-select" required>
                                <option value="goods">Roba</option>
                                <option value="service">Usluga</option>
                                <option value="software">Softver</option>
                                <option value="hardware">Hardver</option>
                                <option value="subscription">Pretplata</option>
                                <option value="other">Ostalo</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Dobavljač</label>
                            <input name="supplier_name" class="app-input">
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Podrijetlo *</label>
                            <select name="supplier_origin" class="app-select" required>
                                <option value="domestic">Domaći</option>
                                <option value="foreign">Strani</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Količina *</label>
                            <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="1" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Nabavna valuta *</label>
                            <select name="purchase_currency" class="app-select" required>
                                <option value="{{ $item->default_purchase_currency }}">{{ $item->default_purchase_currency }}</option>
                                <option value="{{ $item->default_purchase_currency === 'EUR' ? 'USD' : 'EUR' }}">{{ $item->default_purchase_currency === 'EUR' ? 'USD' : 'EUR' }}</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Prodajna valuta *</label>
                            <select name="sale_currency" class="app-select" required>
                                <option value="{{ $item->default_sale_currency }}">{{ $item->default_sale_currency }}</option>
                                <option value="{{ $item->default_sale_currency === 'EUR' ? 'USD' : 'EUR' }}">{{ $item->default_sale_currency === 'EUR' ? 'USD' : 'EUR' }}</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Status flag</label>
                            <input name="status_flag" class="app-input">
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Nabavna / kom *</label>
                            <input type="number" step="0.0001" min="0" name="purchase_net_unit" class="app-input" value="0" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Prodajna / kom *</label>
                            <input type="number" step="0.0001" min="0" name="sale_net_unit" class="app-input" value="0" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Nabavni PDV % *</label>
                            <input type="number" step="0.01" min="0" max="100" name="purchase_vat_rate" class="app-input" value="{{ number_format((float) $item->vat_rate, 2, '.', '') }}" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Prodajni PDV % *</label>
                            <input type="number" step="0.01" min="0" max="100" name="sale_vat_rate" class="app-input" value="{{ number_format((float) $item->vat_rate, 2, '.', '') }}" required>
                        </div>

                        <div class="app-form-group md:col-span-6">
                            <label class="app-label">Opis</label>
                            <textarea name="description" rows="2" class="app-input"></textarea>
                        </div>

                        <div class="app-form-group md:col-span-6">
                            <label class="app-label">Napomena</label>
                            <textarea name="notes" rows="2" class="app-input"></textarea>
                        </div>

                        <div class="app-form-group md:col-span-6">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="is_optional" value="1">
                                <span class="text-sm">Opcionalna stavka</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="app-button">Dodaj stavku</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="app-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold">Troškovi</h3>
                    <div class="text-sm app-muted mt-1">
                        Kompaktni prikaz. Uređivanje se otvara samo za odabrani trošak.
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="app-table w-full">
                    <thead>
                        <tr>
                            <th>Opis</th>
                            <th>Tip</th>
                            <th>Podrijetlo</th>
                            <th>Količina</th>
                            <th>Neto</th>
                            <th>Bruto</th>
                            <th>Ponuda</th>
                            <th>Marža</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($item->costs as $cost)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $cost->description }}</div>
                                    @if($cost->notes)
                                        <div class="text-xs app-muted mt-1">
                                            {{ \Illuminate\Support\Str::limit($cost->notes, 70) }}
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $costTypeLabels[$cost->cost_type] ?? $cost->cost_type }}</td>

                                <td>{{ $cost->supplier_origin === 'foreign' ? 'Strani' : 'Domaći' }}</td>

                                <td>
                                    {{ number_format((float) $cost->quantity, 2, ',', '.') }}
                                    {{ $cost->unit }}
                                </td>

                                <td>
                                    {{ number_format((float) $cost->total_net, 2, ',', '.') }}
                                    {{ $cost->currency }}
                                </td>

                                <td>
                                    {{ number_format((float) $cost->total_gross, 2, ',', '.') }}
                                    {{ $cost->currency }}
                                </td>

                                <td>{{ $cost->include_in_offer ? 'DA' : 'NE' }}</td>
                                <td>{{ $cost->include_in_margin ? 'DA' : 'NE' }}</td>

                                <td class="text-right whitespace-nowrap">
                                    <a
                                        href="{{ route('procurements.edit', ['procurement' => $item->id, 'edit_cost' => $cost->id]) }}#cost-edit-{{ $cost->id }}"
                                        class="app-link mr-3"
                                    >
                                        Uredi
                                    </a>

                                    <form method="POST" action="{{ route('procurements.costs.destroy', [$item, $cost]) }}" class="inline" onsubmit="return confirm('Obrisati trošak?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-link">Obriši</button>
                                    </form>
                                </td>
                            </tr>

                            @if((string) request('edit_cost') === (string) $cost->id)
                                <tr id="cost-edit-{{ $cost->id }}">
                                    <td colspan="9" class="p-0">
                                        <div class="border-t p-4 bg-black/5">
                                            <div class="font-medium mb-4">Uredi trošak</div>

                                            <form method="POST" action="{{ route('procurements.costs.update', [$item, $cost]) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                                                    <div class="app-form-group md:col-span-2">
                                                        <label class="app-label">Opis</label>
                                                        <input name="description" class="app-input" value="{{ $cost->description }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Tip troška</label>
                                                        <select name="cost_type" class="app-select" required>
                                                            @foreach(['field','logistics','shipping','customs','travel','service','other'] as $type)
                                                                <option value="{{ $type }}" @selected($cost->cost_type === $type)>
                                                                    {{ $costTypeLabels[$type] ?? $type }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Podrijetlo</label>
                                                        <select name="supplier_origin" class="app-select" required>
                                                            <option value="domestic" @selected($cost->supplier_origin === 'domestic')>Domaći</option>
                                                            <option value="foreign" @selected($cost->supplier_origin === 'foreign')>Strani</option>
                                                        </select>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Valuta</label>
                                                        <select name="currency" class="app-select" required>
                                                            <option value="EUR" @selected($cost->currency === 'EUR')>EUR</option>
                                                            <option value="USD" @selected($cost->currency === 'USD')>USD</option>
                                                        </select>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Količina</label>
                                                        <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="{{ number_format((float) $cost->quantity, 3, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Jedinica</label>
                                                        <input name="unit" class="app-input" value="{{ $cost->unit }}">
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Neto iznos / kom</label>
                                                        <input type="number" step="0.0001" min="0" name="net_amount" class="app-input" value="{{ number_format((float) $cost->net_amount, 4, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">PDV %</label>
                                                        <input type="number" step="0.01" min="0" max="100" name="vat_rate" class="app-input" value="{{ number_format((float) $cost->vat_rate, 2, '.', '') }}" required>
                                                    </div>

                                                    <div class="app-form-group md:col-span-2">
                                                        <label class="inline-flex items-center gap-2">
                                                            <input type="checkbox" name="include_in_offer" value="1" @checked($cost->include_in_offer)>
                                                            <span class="text-sm">Uključi u ponudu</span>
                                                        </label>
                                                    </div>

                                                    <div class="app-form-group md:col-span-2">
                                                        <label class="inline-flex items-center gap-2">
                                                            <input type="checkbox" name="include_in_margin" value="1" @checked($cost->include_in_margin)>
                                                            <span class="text-sm">Uključi u maržu</span>
                                                        </label>
                                                    </div>

                                                    <div class="app-form-group md:col-span-6">
                                                        <label class="app-label">Napomena</label>
                                                        <textarea name="notes" rows="2" class="app-input">{{ $cost->notes }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="mt-4 flex gap-3">
                                                    <button type="submit" class="app-button-secondary">Spremi trošak</button>

                                                    <a href="{{ route('procurements.edit', $item) }}#costs-nova" class="app-button-secondary">
                                                        Zatvori
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="app-muted">Nema troškova.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="costs-nova" class="border-t mt-6 pt-6">
                <h4 class="font-semibold mb-4">Dodaj novi trošak</h4>

                <form method="POST" action="{{ route('procurements.costs.store', $item) }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="app-form-group md:col-span-2">
                            <label class="app-label">Opis *</label>
                            <input name="description" class="app-input" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Tip troška *</label>
                            <select name="cost_type" class="app-select" required>
                                <option value="field">Terenski trošak</option>
                                <option value="logistics">Logistika</option>
                                <option value="shipping">Dostava</option>
                                <option value="customs">Carina</option>
                                <option value="travel">Putni trošak</option>
                                <option value="service">Usluga</option>
                                <option value="other">Ostalo</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Podrijetlo *</label>
                            <select name="supplier_origin" class="app-select" required>
                                <option value="domestic">Domaći</option>
                                <option value="foreign">Strani</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Valuta *</label>
                            <select name="currency" class="app-select" required>
                                <option value="{{ $item->default_purchase_currency }}">{{ $item->default_purchase_currency }}</option>
                                <option value="{{ $item->default_purchase_currency === 'EUR' ? 'USD' : 'EUR' }}">{{ $item->default_purchase_currency === 'EUR' ? 'USD' : 'EUR' }}</option>
                            </select>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Količina *</label>
                            <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="1" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Jedinica</label>
                            <input name="unit" class="app-input" placeholder="kom, sat, dan, put...">
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">Neto iznos / kom *</label>
                            <input type="number" step="0.0001" min="0" name="net_amount" class="app-input" value="0" required>
                        </div>

                        <div class="app-form-group">
                            <label class="app-label">PDV % *</label>
                            <input type="number" step="0.01" min="0" max="100" name="vat_rate" class="app-input" value="{{ number_format((float) $item->vat_rate, 2, '.', '') }}" required>
                        </div>

                        <div class="app-form-group md:col-span-2">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="include_in_offer" value="1">
                                <span class="text-sm">Uključi u ponudu</span>
                            </label>
                        </div>

                        <div class="app-form-group md:col-span-2">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="include_in_margin" value="1" checked>
                                <span class="text-sm">Uključi u maržu</span>
                            </label>
                        </div>

                        <div class="app-form-group md:col-span-6">
                            <label class="app-label">Napomena</label>
                            <textarea name="notes" rows="2" class="app-input"></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="app-button">Dodaj trošak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection