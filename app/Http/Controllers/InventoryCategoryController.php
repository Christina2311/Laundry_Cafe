<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryCategoryRequest;
use App\Http\Requests\UpdateInventoryCategoryRequest;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;

class InventoryCategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = InventoryCategory::withCount('inventoryItems');

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('category_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query
            ->orderBy('category_name')
            ->paginate(10)
            ->withQueryString();

        return view(
            'inventory.categories.index',
            compact('categories')
        );
    }

    public function create()
    {
        return view('inventory.categories.create');
    }

    public function store(StoreInventoryCategoryRequest $request)
    {
        InventoryCategory::create($request->validated());

        return redirect()
            ->route('inventory.categories.index')
            ->with('success', 'Inventory category created successfully.');
    }

    public function edit(InventoryCategory $inventory_category)
    {
        $category = $inventory_category;

        return view(
            'inventory.categories.edit',
            compact('category')
        );
    }

    public function update(
        UpdateInventoryCategoryRequest $request,
        InventoryCategory $inventory_category
    ) {
        $inventory_category->update($request->validated());

        return redirect()
            ->route('inventory.categories.index')
            ->with('success', 'Inventory category updated successfully.');
    }
}
