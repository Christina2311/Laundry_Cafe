<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitConversionRequest;
use App\Http\Requests\UpdateUnitConversionRequest;
use App\Models\InventoryItem;
use App\Models\MeasurementUnit;
use App\Models\UnitConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UnitConversionController extends Controller
{
    /**
     * Display all unit conversions.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:150'],

            'inventory_item_id' => [
                'nullable',
                'integer',
                'exists:inventory_items,inventory_item_id',
            ],
        ]);

        $query = UnitConversion::with([
            'inventoryItem',
            'fromUnit',
            'toUnit',
        ]);

        // Search by inventory item name.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->whereHas('inventoryItem', function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%");
            });
        }

        // Filter by inventory item.
        if ($request->filled('inventory_item_id')) {
            $query->where(
                'inventory_item_id',
                $request->input('inventory_item_id')
            );
        }

        $conversions = $query
            ->orderBy('inventory_item_id')
            ->orderBy('from_unit_id')
            ->paginate(15)
            ->withQueryString();

        $items = InventoryItem::orderBy('item_name')->get();

        return view(
            'inventory.conversions.index',
            compact('conversions', 'items')
        );
    }

    /**
     * Display the create conversion form.
     */
    public function create()
    {
        $items = InventoryItem::with('baseUnit')
            ->where('is_active', true)
            ->orderBy('item_name')
            ->get();

        $units = MeasurementUnit::orderBy('unit_name')->get();

        return view(
            'inventory.conversions.create',
            compact('items', 'units')
        );
    }

    /**
     * Store a new unit conversion.
     */
    public function store(StoreUnitConversionRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            $item = InventoryItem::lockForUpdate()
                ->findOrFail($validated['inventory_item_id']);

            // Prevent conversions for inactive inventory items.
            if (!$item->is_active) {
                throw ValidationException::withMessages([
                    'inventory_item_id' =>
                    'Cannot create conversions for inactive items.',
                ]);
            }

            $fromUnitId = (int) $validated['from_unit_id'];

            // Automatically assign the item's base unit.
            $toUnitId = (int) $item->base_unit_id;

            // Prevent identical source and destination units.
            if ($fromUnitId === $toUnitId) {
                throw ValidationException::withMessages([
                    'from_unit_id' =>
                    'The source unit cannot be the same as the base unit.',
                ]);
            }

            // Prevent duplicate conversions.
            $exists = UnitConversion::where(
                'inventory_item_id',
                $item->inventory_item_id
            )
                ->where('from_unit_id', $fromUnitId)
                ->where('to_unit_id', $toUnitId)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'from_unit_id' =>
                    'A conversion for this item and source unit already exists.',
                ]);
            }

            UnitConversion::create([
                'inventory_item_id' => $item->inventory_item_id,
                'from_unit_id' => $fromUnitId,
                'to_unit_id' => $toUnitId,
                'conversion_factor' => $validated['conversion_factor'],
            ]);
        });

        return redirect()
            ->route('inventory.conversions.index')
            ->with('success', 'Unit conversion created successfully.');
    }

    /**
     * Display the edit conversion form.
     */
    public function edit(UnitConversion $unit_conversion)
    {
        $conversion = $unit_conversion->load([
            'inventoryItem.baseUnit',
            'fromUnit',
            'toUnit',
        ]);

        return view(
            'inventory.conversions.edit',
            compact('conversion')
        );
    }

    /**
     * Update an existing unit conversion.
     */
    public function update(
        UpdateUnitConversionRequest $request,
        UnitConversion $unit_conversion
    ) {
        $validated = $request->validated();

        DB::transaction(function () use (
            $unit_conversion,
            $validated
        ) {

            // Lock the inventory item first.
            $item = InventoryItem::lockForUpdate()
                ->findOrFail($unit_conversion->inventory_item_id);

            // Lock the conversion record.
            $conversion = UnitConversion::lockForUpdate()
                ->findOrFail($unit_conversion->conversion_id);

            // Prevent modifications for inactive items.
            if (!$item->is_active) {
                throw ValidationException::withMessages([
                    'conversion_factor' =>
                    'Cannot modify conversions for inactive items.',
                ]);
            }

            $conversion->conversion_factor =
                $validated['conversion_factor'];

            // Allow saving without changing the conversion factor.
            if (!$conversion->isDirty('conversion_factor')) {
                return;
            }

            // Protect conversions already used by other records.
            $references = [
                ['purchase_details', 'purchase_unit_id'],
                ['recipe_ingredients', 'unit_id'],
                ['modifier_option_ingredients', 'unit_id'],
                ['service_consumables', 'unit_id'],
            ];

            foreach ($references as [$table, $column]) {

                $inUse = DB::table($table)
                    ->where(
                        'inventory_item_id',
                        $item->inventory_item_id
                    )
                    ->where(
                        $column,
                        $conversion->from_unit_id
                    )
                    ->exists();

                if ($inUse) {
                    throw ValidationException::withMessages([
                        'conversion_factor' =>
                        'This conversion is already in use and cannot be modified.',
                    ]);
                }
            }

            $conversion->save();
        });

        return redirect()
            ->route('inventory.conversions.index')
            ->with('success', 'Unit conversion updated successfully.');
    }
}
