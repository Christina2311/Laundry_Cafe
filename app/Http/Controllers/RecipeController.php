<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\SellableItem;
use App\Models\InventoryItem;
use App\Services\UnitConversionService;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],

            'item_id' => [
                'nullable',
                'integer',
                Rule::exists('sellable_items', 'item_id')
                    ->where('item_type', 'cafe'),
            ],

            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $query = Recipe::whereHas(
            'sellableItem',
            fn($q) => $q->where('item_type', 'cafe')
        )
            ->with('sellableItem.menuItem')
            ->withCount('ingredients');

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where(
                    'recipe_name',
                    'like',
                    "%{$search}%"
                )->orWhereHas(
                    'sellableItem',
                    fn($product) => $product->where(
                        'item_name',
                        'like',
                        "%{$search}%"
                    )
                );
            });
        }

        if (!empty($filters['item_id'])) {
            $query->where('item_id', $filters['item_id']);
        }

        if (($filters['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        }

        if (($filters['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        $recipes = $query
            ->orderByDesc('is_active')
            ->orderBy('recipe_name')
            ->paginate(15)
            ->withQueryString();

        $products = SellableItem::where('item_type', 'cafe')
            ->orderBy('item_name')
            ->get();

        return view('inventory.recipes.index', [
            'recipes' => $recipes,
            'products' => $products,
        ]);
    }

    public function create(Request $request)
    {
        $products = SellableItem::where('item_type', 'cafe')
            ->orderBy('item_name')
            ->get();

        return view('inventory.recipes.create', [
            'products' => $products,
        ]);
    }

    public function store(StoreRecipeRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {

            $product = SellableItem::whereKey($data['item_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCafeProduct($product);

            $this->ensureUniqueName(
                $product->item_id,
                $data['recipe_name']
            );

            Recipe::create([
                'item_id' => $product->item_id,
                'recipe_name' => $data['recipe_name'],
                'is_active' => false,
            ]);
        });

        return redirect()
            ->route('inventory.recipes.index')
            ->with(
                'success',
                'Recipe created. Configure its ingredients before activation.'
            );
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load('sellableItem');

        $this->ensureCafeProduct($recipe->sellableItem);

        return view('inventory.recipes.edit', [
            'recipe' => $recipe,
        ]);
    }

    public function update(
        UpdateRecipeRequest $request,
        Recipe $recipe
    ) {
        $data = $request->validated();

        DB::transaction(function () use ($recipe, $data) {

            $product = SellableItem::whereKey($recipe->item_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCafeProduct($product);

            $lockedRecipe = Recipe::whereKey($recipe->recipe_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureUniqueName(
                $product->item_id,
                $data['recipe_name'],
                $lockedRecipe->recipe_id
            );

            $lockedRecipe->update([
                'recipe_name' => $data['recipe_name'],
            ]);
        });

        return redirect()
            ->route('inventory.recipes.index')
            ->with('success', 'Recipe updated successfully.');
    }

    public function updateStatus(
        Request $request,
        Recipe $recipe,
        UnitConversionService $conversionService
    ) {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use (
            $recipe,
            $validated,
            $conversionService
        ) {
            // Always lock the product first.
            $product = SellableItem::whereKey($recipe->item_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCafeProduct($product);

            $lockedRecipe = Recipe::whereKey($recipe->recipe_id)
                ->lockForUpdate()
                ->firstOrFail();

            $activate = (bool) $validated['is_active'];

            if (!$activate) {
                $lockedRecipe->update([
                    'is_active' => false,
                ]);

                return;
            }

            $ingredients = $lockedRecipe->ingredients()
                ->orderBy('inventory_item_id')
                ->lockForUpdate()
                ->get();

            if ($ingredients->isEmpty()) {
                throw ValidationException::withMessages([
                    'is_active' =>
                    'Add at least one ingredient before activating this recipe.',
                ]);
            }

            // Validate every ingredient and its conversion.
            foreach ($ingredients as $ingredient) {

                $inventoryItem = InventoryItem::whereKey(
                    $ingredient->inventory_item_id
                )->lockForUpdate()->firstOrFail();

                $conversionService->convertToBase(
                    $inventoryItem,
                    $ingredient->unit_id,
                    $ingredient->quantity_required
                );
            }

            // Deactivate any other active recipes.
            Recipe::where('item_id', $product->item_id)
                ->where('recipe_id', '!=', $lockedRecipe->recipe_id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                ]);

            $lockedRecipe->update([
                'is_active' => true,
            ]);
        });

        return redirect()
            ->route('inventory.recipes.index')
            ->with('success', 'Recipe status updated successfully.');
    }

    private function ensureCafeProduct(
        SellableItem $product
    ): void {
        abort_unless($product->item_type === 'cafe', 404);
    }

    private function ensureUniqueName(
        int $itemId,
        string $name,
        ?int $ignoreRecipeId = null
    ): void {
        $query = Recipe::where('item_id', $itemId)
            ->where('recipe_name', $name);

        if ($ignoreRecipeId !== null) {
            $query->where('recipe_id', '!=', $ignoreRecipeId);
        }

        if ($query->lockForUpdate()->first()) {
            throw ValidationException::withMessages([
                'recipe_name' =>
                'This product already has a recipe with that name.',
            ]);
        }
    }
}
