<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\SellableItem;
use App\Http\Requests\StoreSellableItemRequest;
use App\Http\Requests\UpdateSellableItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SellableItemController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],

            'menu_item_id' => [
                'nullable',
                'integer',
                'exists:menu_items,menu_item_id',
            ],

            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $query = SellableItem::where('item_type', 'cafe')
            ->with('menuItem')
            ->withCount([
                'recipes',

                'recipes as active_recipes_count' => fn($q) =>
                $q->where('is_active', true),

                'recipes as configured_recipes_count' => fn($q) =>
                $q->where('is_active', true)
                    ->whereHas('ingredients'),
            ]);

        // Search by product name.
        if (!empty($filters['search'])) {
            $query->where(
                'item_name',
                'like',
                '%' . $filters['search'] . '%'
            );
        }

        // Filter by parent menu item.
        if (!empty($filters['menu_item_id'])) {
            $query->where(
                'menu_item_id',
                $filters['menu_item_id']
            );
        }

        // Filter by product status.
        if (($filters['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        }

        if (($filters['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        $items = $query
            ->orderBy('item_name')
            ->paginate(15)
            ->withQueryString();

        $menuItems = MenuItem::orderBy(
            'menu_item_name'
        )->get();

        return view('inventory.sellable-items.index', [
            'items' => $items,
            'menuItems' => $menuItems,
        ]);
    }

    public function create()
    {
        $menuItems = MenuItem::where('is_active', true)
            ->orderBy('menu_item_name')
            ->get();

        return view('inventory.sellable-items.create', [
            'menuItems' => $menuItems,
        ]);
    }

    public function store(StoreSellableItemRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {

            $menuItem = MenuItem::whereKey(
                $data['menu_item_id']
            )->lockForUpdate()->firstOrFail();

            if (!$menuItem->is_active) {
                throw ValidationException::withMessages([
                    'menu_item_id' =>
                    'The selected menu item is inactive.',
                ]);
            }

            SellableItem::create([
                'menu_item_id' => $menuItem->menu_item_id,
                'item_name' => $data['item_name'],
                'item_type' => 'cafe',
                'current_price' => $data['current_price'],
                'counts_as_load' => false,
                'counts_as_piece' => false,
                'is_active' => true,
            ]);
        });

        return redirect()
            ->route('inventory.sellable-items.index')
            ->with(
                'success',
                'Café product created successfully.'
            );
    }

    public function edit(SellableItem $sellable_item)
    {
        $this->ensureCafeProduct($sellable_item);

        $sellable_item->load('menuItem');

        return view('inventory.sellable-items.edit', [
            'item' => $sellable_item,
        ]);
    }

    public function update(
        UpdateSellableItemRequest $request,
        SellableItem $sellable_item
    ) {
        $this->ensureCafeProduct($sellable_item);

        $sellable_item->update(
            $request->validated()
        );

        return redirect()
            ->route('inventory.sellable-items.index')
            ->with(
                'success',
                'Café product updated successfully.'
            );
    }

    public function updateStatus(
        Request $request,
        SellableItem $sellable_item
    ) {
        $this->ensureCafeProduct($sellable_item);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use (
            $sellable_item,
            $validated
        ) {
            // Lock the parent before the product.
            $menuItem = MenuItem::whereKey(
                $sellable_item->menu_item_id
            )->lockForUpdate()->firstOrFail();

            $item = SellableItem::whereKey(
                $sellable_item->item_id
            )->lockForUpdate()->firstOrFail();

            $this->ensureCafeProduct($item);

            $activate = (bool) $validated['is_active'];

            if ($activate && !$menuItem->is_active) {
                throw ValidationException::withMessages([
                    'is_active' =>
                    'Activate the parent menu item first.',
                ]);
            }

            $item->is_active = $activate;

            $item->save();
        });

        return redirect()
            ->route('inventory.sellable-items.index')
            ->with(
                'success',
                'Café product status updated successfully.'
            );
    }

    private function ensureCafeProduct(
        SellableItem $item
    ): void {
        abort_unless(
            $item->item_type === 'cafe',
            404
        );
    }
}
