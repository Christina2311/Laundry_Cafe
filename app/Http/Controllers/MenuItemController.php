<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $query = MenuItem::withCount('sellableItems');

        // Search by name or description.
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where(
                    'menu_item_name',
                    'like',
                    "%{$search}%"
                )->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );
            });
        }

        // Filter by category.
        if (!empty($filters['category'])) {
            $query->where(
                'category',
                $filters['category']
            );
        }

        // Filter by active status.
        if (($filters['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        }

        if (($filters['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        $menuItems = $query
            ->orderBy('menu_item_name')
            ->paginate(15)
            ->withQueryString();

        $categories = MenuItem::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('inventory.menu-items.index', [
            'menuItems' => $menuItems,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('inventory.menu-items.create');
    }

    public function store(StoreMenuItemRequest $request)
    {
        MenuItem::create([
            ...$request->validated(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('inventory.menu-items.index')
            ->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menu_item)
    {
        return view('inventory.menu-items.edit', [
            'menuItem' => $menu_item,
        ]);
    }

    public function update(
        UpdateMenuItemRequest $request,
        MenuItem $menu_item
    ) {
        $menu_item->update($request->validated());

        return redirect()
            ->route('inventory.menu-items.index')
            ->with('success', 'Menu item updated successfully.');
    }

    public function updateStatus(
        Request $request,
        MenuItem $menu_item
    ) {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use (
            $menu_item,
            $validated
        ) {
            $item = MenuItem::whereKey(
                $menu_item->menu_item_id
            )->lockForUpdate()->firstOrFail();

            $item->is_active = (bool) $validated['is_active'];

            $item->save();
        });

        return redirect()
            ->route('inventory.menu-items.index')
            ->with('success', 'Menu item status updated successfully.');
    }
}
