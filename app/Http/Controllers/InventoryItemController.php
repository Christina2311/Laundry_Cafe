<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryItemController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'category_id' => [
                'nullable',
                'integer',
                'exists:inventory_categories,category_id',
            ],
            'status' => ['nullable', 'in:0,1'],
            'stock' => ['nullable', 'in:low,available'],
        ]);

        $query = InventoryItem::with([
            'category',
            'baseUnit',
        ]);

        // Search inventory items.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where('item_name', 'like', "%{$search}%");
        }

        // Filter by category.
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->input('category_id')
            );
        }

        // Filter by active or inactive status.
        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->input('status')
            );
        }

        // Filter by stock level.
        if ($request->input('stock') === 'low') {
            $query->whereColumn(
                'current_stock',
                '<=',
                'reorder_level'
            );
        }

        if ($request->input('stock') === 'available') {
            $query->whereColumn(
                'current_stock',
                '>',
                'reorder_level'
            );
        }

        $items = $query
            ->orderBy('item_name')
            ->paginate(15)
            ->withQueryString();

        $categories = InventoryCategory::orderBy('category_name')
            ->get();

        return view(
            'inventory.items.index',
            compact('items', 'categories')
        );
    }

    public function create()
    {
        $categories = InventoryCategory::orderBy('category_name')
            ->get();

        $units = MeasurementUnit::orderBy('unit_name')
            ->get();

        return view(
            'inventory.items.create',
            compact('categories', 'units')
        );
    }

    public function store(StoreInventoryItemRequest $request)
    {
        $validated = $request->validated();

        $item = new InventoryItem();

        $item->item_name = $validated['item_name'];
        $item->category_id = $validated['category_id'];
        $item->base_unit_id = $validated['base_unit_id'];
        $item->reorder_level = $validated['reorder_level'];

        // New inventory items always start at zero.
        $item->current_stock = 0;
        $item->is_active = true;

        $item->save();

        return redirect()
            ->route('inventory.items.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function edit(InventoryItem $inventory_item)
    {
        $item = $inventory_item;

        $categories = InventoryCategory::orderBy('category_name')
            ->get();

        $units = MeasurementUnit::orderBy('unit_name')
            ->get();

        return view(
            'inventory.items.edit',
            compact('item', 'categories', 'units')
        );
    }

    public function update(
        UpdateInventoryItemRequest $request,
        InventoryItem $inventory_item
    ) {
        $validated = $request->validated();

        DB::transaction(function () use ($inventory_item, $validated) {

            $item = InventoryItem::lockForUpdate()
                ->findOrFail($inventory_item->inventory_item_id);

            $baseUnitChanged = (int) $item->base_unit_id
                !== (int) $validated['base_unit_id'];

            // Protect the meaning of existing inventory quantities.
            if ($baseUnitChanged) {

                $references = [
                    'stock_movements',
                    'purchase_details',
                    'unit_conversions',
                    'recipe_ingredients',
                    'modifier_option_ingredients',
                    'service_consumables',
                ];

                if ($item->current_stock != 0) {
                    throw ValidationException::withMessages([
                        'base_unit_id' =>
                        'Cannot change the base unit while stock exists.',
                    ]);
                }

                foreach ($references as $table) {

                    if (DB::table($table)
                        ->where(
                            'inventory_item_id',
                            $item->inventory_item_id
                        )
                        ->exists()
                    ) {

                        throw ValidationException::withMessages([
                            'base_unit_id' =>
                            'This item is already in use. Its base unit cannot be changed.',
                        ]);
                    }
                }
            }

            $item->item_name = $validated['item_name'];
            $item->category_id = $validated['category_id'];
            $item->base_unit_id = $validated['base_unit_id'];
            $item->reorder_level = $validated['reorder_level'];

            $item->save();
        });

        return redirect()
            ->route('inventory.items.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function updateStatus(
        Request $request,
        InventoryItem $inventory_item
    ) {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $isActive = (bool) $validated['is_active'];

        DB::transaction(function () use ($inventory_item, $isActive) {

            $item = InventoryItem::lockForUpdate()
                ->findOrFail($inventory_item->inventory_item_id);

            if (!$isActive && $item->current_stock != 0) {
                throw ValidationException::withMessages([
                    'is_active' =>
                    'Cannot deactivate an inventory item with remaining stock.',
                ]);
            }

            $item->is_active = $isActive;
            $item->save();
        });

        return redirect()
            ->route('inventory.items.index')
            ->with(
                'success',
                $isActive
                    ? 'Inventory item activated successfully.'
                    : 'Inventory item deactivated successfully.'
            );
    }
}
