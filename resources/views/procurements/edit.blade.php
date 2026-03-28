@extends('layouts.app')

@section('title', 'Uredi kalkulaciju')

@section('content')

<div class="max-w-7xl">
    <style>
        .proc-grid-compact .app-label {
            font-size: 11px;
            line-height: 1.1;
            margin-bottom: 4px;
            display: block;
        }

        .proc-grid-compact .app-input,
        .proc-grid-compact .app-select {
            min-height: 36px;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .proc-grid-compact textarea.app-input {
            min-height: unset;
        }

        .proc-panel-title {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.2;
        }

        .proc-box {
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 12px;
        }

        .proc-stat-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 8px 0;
            font-size: 13px;
            line-height: 1.25;
        }

        .proc-stat-row + .proc-stat-row {
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .proc-stat-label {
            opacity: .7;
        }

        .proc-stat-value {
            text-align: right;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .proc-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            line-height: 1;
        }

        .proc-section-sep {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: 18px;
            padding-top: 18px;
        }

        .proc-inline-checks {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
        }

        .proc-inline-checks label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .proc-meta-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        @media (min-width: 768px) {
            .proc-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .proc-meta-item {
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding-bottom: 8px;
        }

        .proc-meta-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .proc-meta-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .04em;
            opacity: .65;
            margin-bottom: 4px;
        }

        .proc-meta-value {
            font-size: 13px;
            line-height: 1.3;
            font-weight: 500;
            word-break: break-word;
        }

        .proc-sidebar-section + .proc-sidebar-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .proc-sidebar-highlight .proc-stat-value {
            font-size: 14px;
            font-weight: 700;
        }
    </style>

    @php
        $isMainEdit = request()->boolean('edit_main');

        $itemsCollection = $item->items;
        $costsCollection = $item->costs;

        $itemsPurchaseNet = $itemsCollection->sum(fn ($row) => $row->purchase_net_total);
        $itemsSaleNet = $itemsCollection->sum(fn ($row) => $row->sale_net_total);
        $itemsSaleGross = $itemsCollection->sum(fn ($row) => $row->sale_gross_total);
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
        $vatAmount = ($itemsSaleVat + $offerCostVat) - ($itemsPurchaseVat + $allCostVat);

        $rucValue = $finalProfitNet;
        $rucPercent = $finalPurchaseNet > 0 ? (($rucValue / $finalPurchaseNet) * 100) : 0;

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

    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold leading-tight">{{ $item->title }}</h2>
            <div class="text-sm app-muted mt-1">
                Procurement / kalkulacija / radni ekran
            </div>
        </div>

        <div class="flex gap-2 shrink-0">
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
        <div class="app-card p-3 mb-4">
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

    <div class="space-y-5 proc-grid-compact">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            <div class="xl:col-span-8">
                <div class="app-card p-4">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h3 class="font-semibold leading-tight">Osnovni podaci</h3>
                            <div class="text-sm app-muted mt-1">
                                {{ $isMainEdit ? 'Uređivanje osnovnih parametara dokumenta.' : 'Zaključani pregled dokumenta, partnera i financijskih parametara.' }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="hidden xl:inline-flex proc-chip">ID {{ $item->id }}</span>

                            @if($isMainEdit)
                                <a href="{{ route('procurements.edit', $item) }}" class="app-button-secondary">
                                    Odustani
                                </a>
                            @else
                                <a href="{{ route('procurements.edit', ['procurement' => $item->id, 'edit_main' => 1]) }}" class="app-button-secondary">
                                    Uredi
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($isMainEdit)
                        <form method="POST" action="{{ route('procurements.update', $item) }}">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 xl:grid-cols-12 gap-3">
                                <div class="xl:col-span-4 proc-box">
                                    <div class="proc-panel-title mb-3">Dokument</div>

                                    <div class="space-y-3">
                                        <div class="app-form-group">
                                            <label class="app-label">Naziv kalkulacije *</label>
                                            <input
                                                name="title"
                                                class="app-input"
                                                value="{{ old('title', $item->title) }}"
                                                required
                                            >
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="app-form-group">
                                                <label class="app-label">Referentni broj</label>
                                                <input
                                                    name="reference_no"
                                                    class="app-input"
                                                    value="{{ old('reference_no', $item->reference_no) }}"
                                                >
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
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
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
                                        </div>
                                    </div>
                                </div>

                                <div class="xl:col-span-4 proc-box">
                                    <div class="proc-panel-title mb-3">Partner i kontekst</div>

                                    <div class="space-y-3">
                                        <div class="app-form-group">
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

                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="rounded-lg border border-white/10 px-3 py-2">
                                                <div class="text-[10px] uppercase tracking-wide app-muted">Kreirano</div>
                                                <div class="text-xs mt-1">{{ $item->created_at->format('d.m.Y H:i') }}</div>
                                            </div>

                                            <div class="rounded-lg border border-white/10 px-3 py-2">
                                                <div class="text-[10px] uppercase tracking-wide app-muted">Status</div>
                                                <div class="text-xs mt-1">{{ $statuses[$item->status] ?? $item->status }}</div>
                                            </div>
                                        </div>

                                        <div class="rounded-lg border border-white/10 px-3 py-2">
                                            <div class="text-[10px] uppercase tracking-wide app-muted">Valuta</div>
                                            <div class="text-xs mt-1">EUR</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="xl:col-span-4 proc-box">
                                    <div class="proc-panel-title mb-3">Financije</div>

                                    <div class="space-y-3">
                                        <div class="app-form-group">
                                            <label class="app-label">Stopa PDV-a (%) *</label>
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

                                        <div class="rounded-lg border border-white/10 px-3 py-2">
                                            <div class="text-[10px] uppercase tracking-wide app-muted">Napomena</div>
                                            <div class="text-xs mt-1">
                                                Sve kalkulacije u ovom modulu vode se u EUR.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="app-form-group">
                                    <label class="app-label">Napomena</label>
                                    <textarea name="notes" rows="3" class="app-input">{{ old('notes', $item->notes) }}</textarea>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <button class="app-button" type="submit">Spremi promjene</button>
                                <a href="{{ route('procurements.edit', $item) }}" class="app-button-secondary">Odustani</a>
                            </div>
                        </form>
                    @else
                        <div class="grid grid-cols-1 xl:grid-cols-12 gap-3">
                            <div class="xl:col-span-4 proc-box">
                                <div class="proc-panel-title mb-3">Dokument</div>

                                <div class="proc-meta-grid">
                                    <div class="proc-meta-item">
                                        <div class="proc-meta-label">Naziv kalkulacije</div>
                                        <div class="proc-meta-value">{{ $item->title ?: '-' }}</div>
                                    </div>

                                    <div class="proc-meta-item">
                                        <div class="proc-meta-label">Status</div>
                                        <div class="proc-meta-value">{{ $statuses[$item->status] ?? $item->status }}</div>
                                    </div>

                                    <div class="proc-meta-item">
                                        <div class="proc-meta-label">Referentni broj</div>
                                        <div class="proc-meta-value">{{ $item->reference_no ?: '-' }}</div>
                                    </div>

                                    <div class="proc-meta-item">
                                        <div class="proc-meta-label">Datum ponude</div>
                                        <div class="proc-meta-value">{{ optional($item->offer_date)->format('d.m.Y') ?: '-' }}</div>
                                    </div>

                                    <div class="proc-meta-item md:col-span-2">
                                        <div class="proc-meta-label">Vrijedi do</div>
                                        <div class="proc-meta-value">{{ optional($item->valid_until)->format('d.m.Y') ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="xl:col-span-4 proc-box">
                                <div class="proc-panel-title mb-3">Partner i kontekst</div>

                                <div class="proc-meta-grid">
                                    <div class="proc-meta-item md:col-span-2">
                                        <div class="proc-meta-label">Partner</div>
                                        <div class="proc-meta-value">{{ $item->partner->name ?? '-' }}</div>
                                    </div>

                                    <div class="proc-meta-item">
                                        <div class="proc-meta-label">Kreirano</div>
                                        <div class="proc-meta-value">{{ $item->created_at->format('d.m.Y H:i') }}</div>
                                    </div>

                                    <div class="proc-meta-item">
                                        <div class="proc-meta-label">ID dokumenta</div>
                                        <div class="proc-meta-value">{{ $item->id }}</div>
                                    </div>

                                    <div class="proc-meta-item md:col-span-2">
                                        <div class="proc-meta-label">Valuta</div>
                                        <div class="proc-meta-value">EUR</div>
                                    </div>
                                </div>
                            </div>

                            <div class="xl:col-span-4 proc-box">
                                <div class="proc-panel-title mb-3">Financije</div>

                                <div class="proc-meta-grid">
                                    <div class="proc-meta-item md:col-span-2">
                                        <div class="proc-meta-label">Stopa PDV-a</div>
                                        <div class="proc-meta-value">{{ number_format((float) $item->vat_rate, 2, ',', '.') }} %</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($item->notes)
                            <div class="mt-3 proc-box">
                                <div class="proc-panel-title mb-2">Napomena</div>
                                <div class="text-sm leading-relaxed whitespace-pre-line">{{ $item->notes }}</div>
                            </div>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('procurements.edit', ['procurement' => $item->id, 'edit_main' => 1]) }}" class="app-button-secondary">
                                Uredi osnovne podatke
                            </a>
                            <a href="#items-nova" class="app-button-secondary">Dodaj stavku</a>
                            <a href="#costs-nova" class="app-button-secondary">Dodaj trošak</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="xl:col-span-4">
                <div class="app-card p-4 xl:sticky xl:top-4">
                    <div class="space-y-5">
                        <div class="proc-sidebar-section">
                            <h3 class="font-semibold mb-3">Sažetak</h3>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Partner</div>
                                <div class="proc-stat-value">{{ $item->partner->name ?? '-' }}</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Status</div>
                                <div class="proc-stat-value">{{ $statuses[$item->status] ?? $item->status }}</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Valuta</div>
                                <div class="proc-stat-value">EUR</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Kreirano</div>
                                <div class="proc-stat-value">{{ $item->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>

                        <div class="proc-sidebar-section">
                            <h3 class="font-semibold mb-3">Po vrstama troška</h3>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Stavke nabavna neto</div>
                                <div class="proc-stat-value">{{ number_format((float) $itemsPurchaseNet, 2, ',', '.') }} €</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Trošak terena</div>
                                <div class="proc-stat-value">{{ number_format((float) $fieldCostNet, 2, ',', '.') }} €</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Ostali troškovi</div>
                                <div class="proc-stat-value">{{ number_format((float) $otherCostNet, 2, ',', '.') }} €</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Ukupno troškovi</div>
                                <div class="proc-stat-value">{{ number_format((float) $allCostNet, 2, ',', '.') }} €</div>
                            </div>

                            <div class="proc-stat-row proc-sidebar-highlight">
                                <div class="proc-stat-label">Ukupna nabavna osnova</div>
                                <div class="proc-stat-value">{{ number_format((float) $finalPurchaseNet, 2, ',', '.') }} €</div>
                            </div>
                        </div>

                        <div class="proc-sidebar-section">
                            <h3 class="font-semibold mb-3">Prihod</h3>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Prodajna neto</div>
                                <div class="proc-stat-value">{{ number_format((float) $finalSaleNet, 2, ',', '.') }} €</div>
                            </div>

                            <div class="proc-stat-row proc-sidebar-highlight">
                                <div class="proc-stat-label">Bruto prodajna</div>
                                <div class="proc-stat-value">{{ number_format((float) $finalSaleGross, 2, ',', '.') }} €</div>
                            </div>
                        </div>

                        <div class="proc-sidebar-section">
                            <h3 class="font-semibold mb-3">Porez</h3>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Stopa PDV-a</div>
                                <div class="proc-stat-value">{{ number_format((float) $item->vat_rate, 2, ',', '.') }} %</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Iznos PDV-a</div>
                                <div class="proc-stat-value">{{ number_format((float) $vatAmount, 2, ',', '.') }} €</div>
                            </div>
                        </div>

                        <div class="proc-sidebar-section">
                            <h3 class="font-semibold mb-3">Rezultat</h3>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">Marža neto</div>
                                <div class="proc-stat-value">{{ number_format((float) $finalProfitNet, 2, ',', '.') }} €</div>
                            </div>

                            <div class="proc-stat-row proc-sidebar-highlight">
                                <div class="proc-stat-label">RUC</div>
                                <div class="proc-stat-value">{{ number_format((float) $rucValue, 2, ',', '.') }} €</div>
                            </div>

                            <div class="proc-stat-row">
                                <div class="proc-stat-label">RUC %</div>
                                <div class="proc-stat-value">{{ number_format((float) $rucPercent, 2, ',', '.') }} %</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-semibold">Stavke kalkulacije</h3>
                    <div class="text-sm app-muted mt-1">
                        Tablica + brzi operativni unos.
                    </div>
                </div>

                <a href="#items-nova" class="app-button-secondary whitespace-nowrap">
                    Nova stavka
                </a>
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

                                <td>{{ number_format((float) $row->purchase_net_total, 2, ',', '.') }} €</td>

                                <td>{{ number_format((float) $row->sale_net_total, 2, ',', '.') }} €</td>

                                <td>{{ number_format((float) $row->sale_gross_total, 2, ',', '.') }} €</td>

                                <td>{{ number_format((float) $row->profit_net, 2, ',', '.') }} €</td>

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

                                                <div class="space-y-3">
                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                                        <div class="app-form-group md:col-span-3">
                                                            <label class="app-label">Naziv *</label>
                                                            <input name="name" class="app-input" value="{{ $row->name }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-2">
                                                            <label class="app-label">Tip *</label>
                                                            <select name="item_type" class="app-select" required>
                                                                @foreach(['goods','service','software','hardware','subscription','other'] as $type)
                                                                    <option value="{{ $type }}" @selected($row->item_type === $type)>
                                                                        {{ $itemTypeLabels[$type] ?? $type }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="app-form-group md:col-span-2">
                                                            <label class="app-label">Dobavljač</label>
                                                            <input name="supplier_name" class="app-input" value="{{ $row->supplier_name }}">
                                                        </div>

                                                        <div class="app-form-group md:col-span-2">
                                                            <label class="app-label">Podrijetlo *</label>
                                                            <select name="supplier_origin" class="app-select" required>
                                                                <option value="domestic" @selected($row->supplier_origin === 'domestic')>Domaći</option>
                                                                <option value="foreign" @selected($row->supplier_origin === 'foreign')>Strani</option>
                                                            </select>
                                                        </div>

                                                        <div class="app-form-group md:col-span-1">
                                                            <label class="app-label">Količina *</label>
                                                            <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="{{ number_format((float) $row->quantity, 3, '.', '') }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-2">
                                                            <label class="app-label">Status flag</label>
                                                            <input name="status_flag" class="app-input" value="{{ $row->status_flag }}">
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                                        <div class="app-form-group md:col-span-3">
                                                            <label class="app-label">Nabavna / kom *</label>
                                                            <input type="number" step="0.0001" min="0" name="purchase_net_unit" class="app-input" value="{{ number_format((float) $row->purchase_net_unit, 4, '.', '') }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-3">
                                                            <label class="app-label">Prodajna / kom *</label>
                                                            <input type="number" step="0.0001" min="0" name="sale_net_unit" class="app-input" value="{{ number_format((float) $row->sale_net_unit, 4, '.', '') }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-3">
                                                            <label class="app-label">Nabavni PDV % *</label>
                                                            <input type="number" step="0.01" min="0" max="100" name="purchase_vat_rate" class="app-input" value="{{ number_format((float) $row->purchase_vat_rate, 2, '.', '') }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-3">
                                                            <label class="app-label">Prodajni PDV % *</label>
                                                            <input type="number" step="0.01" min="0" max="100" name="sale_vat_rate" class="app-input" value="{{ number_format((float) $row->sale_vat_rate, 2, '.', '') }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                        <div class="app-form-group">
                                                            <label class="app-label">Opis</label>
                                                            <textarea name="description" rows="2" class="app-input">{{ $row->description }}</textarea>
                                                        </div>

                                                        <div class="app-form-group">
                                                            <label class="app-label">Napomena</label>
                                                            <textarea name="notes" rows="2" class="app-input">{{ $row->notes }}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="proc-inline-checks">
                                                        <label>
                                                            <input type="checkbox" name="is_optional" value="1" @checked($row->is_optional)>
                                                            <span>Opcionalna stavka</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mt-4 flex gap-3">
                                                    <button type="submit" class="app-button-secondary">Spremi stavku</button>
                                                    <a href="{{ route('procurements.edit', $item) }}#items-nova" class="app-button-secondary">Zatvori</a>
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

            <div id="items-nova" class="proc-section-sep">
                <div class="mb-4">
                    <h4 class="font-semibold">Dodaj novu stavku</h4>
                    <div class="text-sm app-muted mt-1">
                        Glavni unos je složen po operativnom redu rada.
                    </div>
                </div>

                <form method="POST" action="{{ route('procurements.items.store', $item) }}">
                    @csrf

                    <div class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <div class="app-form-group md:col-span-4">
                                <label class="app-label">Naziv *</label>
                                <input name="name" class="app-input" required>
                            </div>

                            <div class="app-form-group md:col-span-2">
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

                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Dobavljač</label>
                                <input name="supplier_name" class="app-input">
                            </div>

                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Podrijetlo *</label>
                                <select name="supplier_origin" class="app-select" required>
                                    <option value="domestic">Domaći</option>
                                    <option value="foreign">Strani</option>
                                </select>
                            </div>

                            <div class="app-form-group md:col-span-1">
                                <label class="app-label">Količina *</label>
                                <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="1" required>
                            </div>

                            <div class="app-form-group md:col-span-1">
                                <label class="app-label">Flag</label>
                                <input name="status_flag" class="app-input">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <div class="app-form-group md:col-span-3">
                                <label class="app-label">Nabavna / kom *</label>
                                <input type="number" step="0.0001" min="0" name="purchase_net_unit" class="app-input" value="0" required>
                            </div>

                            <div class="app-form-group md:col-span-3">
                                <label class="app-label">Prodajna / kom *</label>
                                <input type="number" step="0.0001" min="0" name="sale_net_unit" class="app-input" value="0" required>
                            </div>

                            <div class="app-form-group md:col-span-3">
                                <label class="app-label">Nabavni PDV % *</label>
                                <input type="number" step="0.01" min="0" max="100" name="purchase_vat_rate" class="app-input" value="{{ number_format((float) $item->vat_rate, 2, '.', '') }}" required>
                            </div>

                            <div class="app-form-group md:col-span-3">
                                <label class="app-label">Prodajni PDV % *</label>
                                <input type="number" step="0.01" min="0" max="100" name="sale_vat_rate" class="app-input" value="{{ number_format((float) $item->vat_rate, 2, '.', '') }}" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="app-form-group">
                                <label class="app-label">Opis</label>
                                <textarea name="description" rows="2" class="app-input"></textarea>
                            </div>

                            <div class="app-form-group">
                                <label class="app-label">Napomena</label>
                                <textarea name="notes" rows="2" class="app-input"></textarea>
                            </div>
                        </div>

                        <div class="proc-inline-checks">
                            <label>
                                <input type="checkbox" name="is_optional" value="1">
                                <span>Opcionalna stavka</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="app-button">Dodaj stavku</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-semibold">Troškovi</h3>
                    <div class="text-sm app-muted mt-1">
                        Brzi unos troškova bez nepotrebnog razvlačenja forme.
                    </div>
                </div>

                <a href="#costs-nova" class="app-button-secondary whitespace-nowrap">
                    Novi trošak
                </a>
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

                                <td>{{ number_format((float) $cost->total_net, 2, ',', '.') }} €</td>

                                <td>{{ number_format((float) $cost->total_gross, 2, ',', '.') }} €</td>

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

                                                <div class="space-y-3">
                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                                        <div class="app-form-group md:col-span-3">
                                                            <label class="app-label">Opis *</label>
                                                            <input name="description" class="app-input" value="{{ $cost->description }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-2">
                                                            <label class="app-label">Tip troška *</label>
                                                            <select name="cost_type" class="app-select" required>
                                                                @foreach(['field','logistics','shipping','customs','travel','service','other'] as $type)
                                                                    <option value="{{ $type }}" @selected($cost->cost_type === $type)>
                                                                        {{ $costTypeLabels[$type] ?? $type }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="app-form-group md:col-span-2">
                                                            <label class="app-label">Podrijetlo *</label>
                                                            <select name="supplier_origin" class="app-select" required>
                                                                <option value="domestic" @selected($cost->supplier_origin === 'domestic')>Domaći</option>
                                                                <option value="foreign" @selected($cost->supplier_origin === 'foreign')>Strani</option>
                                                            </select>
                                                        </div>

                                                        <div class="app-form-group md:col-span-2">
                                                            <label class="app-label">Količina *</label>
                                                            <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="{{ number_format((float) $cost->quantity, 3, '.', '') }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-3">
                                                            <label class="app-label">Jedinica</label>
                                                            <input name="unit" class="app-input" value="{{ $cost->unit }}">
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                                        <div class="app-form-group md:col-span-4">
                                                            <label class="app-label">Neto iznos / kom *</label>
                                                            <input type="number" step="0.0001" min="0" name="net_amount" class="app-input" value="{{ number_format((float) $cost->net_amount, 4, '.', '') }}" required>
                                                        </div>

                                                        <div class="app-form-group md:col-span-4">
                                                            <label class="app-label">PDV % *</label>
                                                            <input type="number" step="0.01" min="0" max="100" name="vat_rate" class="app-input" value="{{ number_format((float) $cost->vat_rate, 2, '.', '') }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="proc-inline-checks">
                                                        <label>
                                                            <input type="checkbox" name="include_in_offer" value="1" @checked($cost->include_in_offer)>
                                                            <span>Uključi u ponudu</span>
                                                        </label>

                                                        <label>
                                                            <input type="checkbox" name="include_in_margin" value="1" @checked($cost->include_in_margin)>
                                                            <span>Uključi u maržu</span>
                                                        </label>
                                                    </div>

                                                    <div class="app-form-group">
                                                        <label class="app-label">Napomena</label>
                                                        <textarea name="notes" rows="2" class="app-input">{{ $cost->notes }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="mt-4 flex gap-3">
                                                    <button type="submit" class="app-button-secondary">Spremi trošak</button>
                                                    <a href="{{ route('procurements.edit', $item) }}#costs-nova" class="app-button-secondary">Zatvori</a>
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

            <div id="costs-nova" class="proc-section-sep">
                <div class="mb-4">
                    <h4 class="font-semibold">Dodaj novi trošak</h4>
                    <div class="text-sm app-muted mt-1">
                        Osnovni operativni unos troška u 2 reda + dodatni detalji.
                    </div>
                </div>

                <form method="POST" action="{{ route('procurements.costs.store', $item) }}">
                    @csrf

                    <div class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <div class="app-form-group md:col-span-4">
                                <label class="app-label">Opis *</label>
                                <input name="description" class="app-input" required>
                            </div>

                            <div class="app-form-group md:col-span-2">
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

                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Podrijetlo *</label>
                                <select name="supplier_origin" class="app-select" required>
                                    <option value="domestic">Domaći</option>
                                    <option value="foreign">Strani</option>
                                </select>
                            </div>

                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Količina *</label>
                                <input type="number" step="0.001" min="0.001" name="quantity" class="app-input" value="1" required>
                            </div>

                            <div class="app-form-group md:col-span-2">
                                <label class="app-label">Jedinica</label>
                                <input name="unit" class="app-input" placeholder="kom, sat, dan, put...">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <div class="app-form-group md:col-span-4">
                                <label class="app-label">Neto iznos / kom *</label>
                                <input type="number" step="0.0001" min="0" name="net_amount" class="app-input" value="0" required>
                            </div>

                            <div class="app-form-group md:col-span-4">
                                <label class="app-label">PDV % *</label>
                                <input type="number" step="0.01" min="0" max="100" name="vat_rate" class="app-input" value="{{ number_format((float) $item->vat_rate, 2, '.', '') }}" required>
                            </div>
                        </div>

                        <div class="proc-inline-checks">
                            <label>
                                <input type="checkbox" name="include_in_offer" value="1">
                                <span>Uključi u ponudu</span>
                            </label>

                            <label>
                                <input type="checkbox" name="include_in_margin" value="1" checked>
                                <span>Uključi u maržu</span>
                            </label>
                        </div>

                        <div class="app-form-group">
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