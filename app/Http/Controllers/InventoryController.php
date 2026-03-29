<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    protected array $readOnlyColumns = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function index(Request $request)
    {
        $columns = Schema::getColumnListing('inventory_items');

        $query = DB::table('inventory_items')
            ->leftJoin('partners', 'inventory_items.partner_id', '=', 'partners.id')
            ->select('inventory_items.*', 'partners.name as partner_name');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search, $columns) {
                foreach (['hostname', 'device_name', 'agent_device_id', 'serial_number', 'model', 'manufacturer'] as $field) {
                    if (in_array($field, $columns, true)) {
                        $q->orWhere('inventory_items.' . $field, 'like', '%' . $search . '%');
                    }
                }

                $q->orWhere('partners.name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('partner_id') && in_array('partner_id', $columns, true)) {
            $query->where('inventory_items.partner_id', $request->integer('partner_id'));
        }

        if (in_array('updated_at', $columns, true)) {
            $query->orderByDesc('inventory_items.updated_at');
        } else {
            $query->orderByDesc('inventory_items.id');
        }

        $items = $query->paginate(25)->withQueryString();

        $partners = DB::table('partners')->orderBy('name')->get(['id', 'name']);

        return view('inventory.index', compact('items', 'partners'));
    }

    public function show(int $id)
    {
        $item = DB::table('inventory_items')->where('id', $id)->first();
        abort_unless($item, 404);

        $software = [];
        if (Schema::hasTable('inventory_software')) {
            $softwareColumns = Schema::getColumnListing('inventory_software');

            if (in_array('inventory_item_id', $softwareColumns, true)) {
                $software = DB::table('inventory_software')
                    ->where('inventory_item_id', $id)
                    ->orderBy('name')
                    ->get();
            } elseif (
                in_array('partner_id', $softwareColumns, true) &&
                in_array('agent_device_id', $softwareColumns, true)
            ) {
                $software = DB::table('inventory_software')
                    ->where('partner_id', $item->partner_id)
                    ->where('agent_device_id', $item->agent_device_id)
                    ->orderBy('name')
                    ->get();
            }
        }

        $scans = [];
        if (Schema::hasTable('inventory_scans')) {
            $scanColumns = Schema::getColumnListing('inventory_scans');

            if (in_array('inventory_item_id', $scanColumns, true)) {
                $scans = DB::table('inventory_scans')
                    ->where('inventory_item_id', $id)
                    ->orderByDesc('id')
                    ->get();
            } elseif (
                in_array('partner_id', $scanColumns, true) &&
                in_array('agent_device_id', $scanColumns, true)
            ) {
                $scans = DB::table('inventory_scans')
                    ->where('partner_id', $item->partner_id)
                    ->where('agent_device_id', $item->agent_device_id)
                    ->orderByDesc('id')
                    ->get();
            }
        }

        $partner = null;
        if (!empty($item->partner_id)) {
            $partner = DB::table('partners')->where('id', $item->partner_id)->first();
        }

        return view('inventory.show', [
            'item' => $item,
            'partner' => $partner,
            'software' => $software,
            'scans' => $scans,
        ]);
    }

    public function create()
    {
        $columns = array_values(array_filter(
            Schema::getColumnListing('inventory_items'),
            fn ($column) => !in_array($column, $this->readOnlyColumns, true)
        ));

        $partners = DB::table('partners')->orderBy('name')->get(['id', 'name']);

        return view('inventory.form', [
            'item' => null,
            'columns' => $columns,
            'partners' => $partners,
        ]);
    }

    public function store(Request $request)
    {
        $columns = Schema::getColumnListing('inventory_items');

        $rules = $this->rules($columns);
        $data = $request->validate($rules);

        foreach (['inventory_enabled', 'is_internal'] as $boolColumn) {
            if (in_array($boolColumn, $columns, true)) {
                $data[$boolColumn] = $request->boolean($boolColumn);
            }
        }

        if (in_array('created_at', $columns, true)) {
            $data['created_at'] = now();
        }

        if (in_array('updated_at', $columns, true)) {
            $data['updated_at'] = now();
        }

        $id = DB::table('inventory_items')->insertGetId($data);

        return redirect()->route('inventory.show', $id)
            ->with('success', 'Inventory uređaj je dodan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('inventory_items')->where('id', $id)->first();
        abort_unless($item, 404);

        $columns = array_values(array_filter(
            Schema::getColumnListing('inventory_items'),
            fn ($column) => !in_array($column, $this->readOnlyColumns, true)
        ));

        $partners = DB::table('partners')->orderBy('name')->get(['id', 'name']);

        return view('inventory.form', compact('item', 'columns', 'partners'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('inventory_items')->where('id', $id)->first();
        abort_unless($item, 404);

        $columns = Schema::getColumnListing('inventory_items');
        $rules = $this->rules($columns, $id);
        $data = $request->validate($rules);

        foreach (['inventory_enabled', 'is_internal'] as $boolColumn) {
            if (in_array($boolColumn, $columns, true)) {
                $data[$boolColumn] = $request->boolean($boolColumn);
            }
        }

        if (in_array('updated_at', $columns, true)) {
            $data['updated_at'] = now();
        }

        DB::table('inventory_items')->where('id', $id)->update($data);

        return redirect()->route('inventory.show', $id)
            ->with('success', 'Inventory uređaj je ažuriran.');
    }

    public function destroy(int $id)
    {
        DB::table('inventory_items')->where('id', $id)->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory uređaj je obrisan.');
    }

    protected function rules(array $columns, ?int $ignoreId = null): array
    {
        $rules = [];

        foreach ($columns as $column) {
            if (in_array($column, $this->readOnlyColumns, true)) {
                continue;
            }

            $rules[$column] = ['nullable'];

            if ($column === 'partner_id') {
                $rules[$column][] = 'integer';
                $rules[$column][] = 'exists:partners,id';
                continue;
            }

            if (in_array($column, ['inventory_enabled', 'is_internal'], true)) {
                $rules[$column][] = 'boolean';
                continue;
            }

            if ($column === 'agent_device_id') {
                $unique = Rule::unique('inventory_items', 'agent_device_id');

                if (in_array('partner_id', $columns, true)) {
                    $unique->where(fn ($q) => $q->where('partner_id', request('partner_id')));
                }

                if ($ignoreId) {
                    $unique->ignore($ignoreId);
                }

                $rules[$column][] = 'string';
                $rules[$column][] = 'max:255';
                $rules[$column][] = $unique;
                continue;
            }

            $rules[$column][] = 'string';
        }

        return $rules;
    }
}