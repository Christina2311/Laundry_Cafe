<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\SellableItem;
use App\Models\InventoryItem;
use App\Services\UnitConversionService;
use App\Http\Requests\StoreRecipeIngredientRequest;
use App\Http\Requests\UpdateRecipeIngredientRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecipeIngredientController extends Controller
{
    public function index(Recipe $recipe)
    {
        $this->ensureCafeRecipe($recipe);

        $recipe->load([
            'sellableItem.menuItem',
            'ingredients.inventoryItem.baseUnit',
            'ingredients.unit',
        ]);

        $inventoryItems = InventoryItem::where(
            'is_active',
            true
        )
            ->with([
                'baseUnit',
                'unitConversions.fromUnit',
            ])
            ->orderBy('item_name')
            ->get();

        $inventoryOptions = $inventoryItems->map(
            fn($item) => [
                'id' => $item->inventory_item_id,
                'name' => $item->item_name,
                'units' => $this->availableUnits($item),
            ]
        )->values();

        return view('inventory.recipe-ingredients.index', [
            'recipe' => $recipe,
            'inventoryOptions' => $inventoryOptions,
        ]);
    }

    public function store(
        StoreRecipeIngredientRequest $request,
        Recipe $recipe,
        UnitConversionService $conversionService
    ) {
        $data = $request->validated();

        DB::transaction(function () use (
            $recipe,
            $data,
            $conversionService
        ) {
            $lockedRecipe = $this->lockEditableRecipe($recipe);

            $inventoryItem = InventoryItem::whereKey(
                $data['inventory_item_id']
            )->lockForUpdate()->firstOrFail();

            $duplicate = $lockedRecipe->ingredients()
                ->where(
                    'inventory_item_id',
                    $inventoryItem->inventory_item_id
                )
                ->lockForUpdate()
                ->first();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'inventory_item_id' =>
                    'This ingredient already exists in the recipe.',
                ]);
            }

            $this->validateConversion(
                $conversionService,
                $inventoryItem,
                $data['unit_id'],
                $data['quantity_required']
            );

            $lockedRecipe->ingredients()->create([
                'inventory_item_id' =>
                $inventoryItem->inventory_item_id,

                'quantity_required' =>
                $data['quantity_required'],

                'unit_id' => $data['unit_id'],
            ]);
        });

        return redirect()
            ->route(
                'inventory.recipes.ingredients.index',
                $recipe
            )
            ->with('success', 'Ingredient added successfully.');
    }

    public function edit(
        Recipe $recipe,
        RecipeIngredient $ingredient
    ) {
        $this->ensureCafeRecipe($recipe);

        $this->ensureIngredientBelongsToRecipe(
            $recipe,
            $ingredient
        );

        if ($recipe->is_active) {
            return redirect()
                ->route(
                    'inventory.recipes.ingredients.index',
                    $recipe
                )
                ->withErrors([
                    'recipe' =>
                    'Deactivate the recipe before editing ingredients.',
                ]);
        }

        $ingredient->load([
            'inventoryItem.baseUnit',
            'inventoryItem.unitConversions.fromUnit',
            'unit',
        ]);

        return view('inventory.recipe-ingredients.edit', [
            'recipe' => $recipe,
            'ingredient' => $ingredient,
            'units' => $this->availableUnits(
                $ingredient->inventoryItem
            ),
        ]);
    }

    public function update(
        UpdateRecipeIngredientRequest $request,
        Recipe $recipe,
        RecipeIngredient $ingredient,
        UnitConversionService $conversionService
    ) {
        $data = $request->validated();

        DB::transaction(function () use (
            $recipe,
            $ingredient,
            $data,
            $conversionService
        ) {
            $lockedRecipe = $this->lockEditableRecipe($recipe);

            $lockedIngredient = $lockedRecipe->ingredients()
                ->whereKey($ingredient->recipe_ingredient_id)
                ->lockForUpdate()
                ->firstOrFail();

            $inventoryItem = InventoryItem::whereKey(
                $lockedIngredient->inventory_item_id
            )->lockForUpdate()->firstOrFail();

            $this->validateConversion(
                $conversionService,
                $inventoryItem,
                $data['unit_id'],
                $data['quantity_required']
            );

            $lockedIngredient->update([
                'quantity_required' =>
                $data['quantity_required'],

                'unit_id' => $data['unit_id'],
            ]);
        });

        return redirect()
            ->route(
                'inventory.recipes.ingredients.index',
                $recipe
            )
            ->with('success', 'Ingredient updated successfully.');
    }

    public function destroy(
        Recipe $recipe,
        RecipeIngredient $ingredient
    ) {
        DB::transaction(function () use (
            $recipe,
            $ingredient
        ) {
            $lockedRecipe = $this->lockEditableRecipe($recipe);

            $lockedIngredient = $lockedRecipe->ingredients()
                ->whereKey($ingredient->recipe_ingredient_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedIngredient->delete();
        });

        return redirect()
            ->route(
                'inventory.recipes.ingredients.index',
                $recipe
            )
            ->with('success', 'Ingredient removed successfully.');
    }

    private function lockEditableRecipe(
        Recipe $recipe
    ): Recipe {
        // Follow the same locking order as RecipeController.
        $product = SellableItem::whereKey($recipe->item_id)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless($product->item_type === 'cafe', 404);

        $lockedRecipe = Recipe::whereKey($recipe->recipe_id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedRecipe->is_active) {
            throw ValidationException::withMessages([
                'recipe' =>
                'Deactivate the recipe before modifying ingredients.',
            ]);
        }

        return $lockedRecipe;
    }

    private function ensureCafeRecipe(Recipe $recipe): void
    {
        abort_unless(
            $recipe->sellableItem?->item_type === 'cafe',
            404
        );
    }

    private function ensureIngredientBelongsToRecipe(
        Recipe $recipe,
        RecipeIngredient $ingredient
    ): void {
        abort_unless(
            (int) $ingredient->recipe_id ===
                (int) $recipe->recipe_id,
            404
        );
    }

    private function availableUnits(
        InventoryItem $item
    ): array {
        $units = [
            [
                'id' => $item->base_unit_id,
                'label' => $item->baseUnit->unit_name
                    . ' (' . $item->baseUnit->abbreviation . ')',
            ],
        ];

        foreach ($item->unitConversions as $conversion) {

            if (
                (int) $conversion->to_unit_id !==
                (int) $item->base_unit_id
            ) {
                continue;
            }

            $units[] = [
                'id' => $conversion->from_unit_id,
                'label' => $conversion->fromUnit->unit_name
                    . ' (' . $conversion->fromUnit->abbreviation . ')',
            ];
        }

        return $units;
    }

    private function validateConversion(
        UnitConversionService $service,
        InventoryItem $item,
        int $unitId,
        string|int $quantity
    ): void {
        if (!$item->is_active) {
            throw ValidationException::withMessages([
                'inventory_item_id' =>
                'The selected inventory item is inactive.',
            ]);
        }

        try {
            $service->convertToBase(
                $item,
                $unitId,
                $quantity
            );
        } catch (\InvalidArgumentException | \DomainException $e) {
            throw ValidationException::withMessages([
                'quantity_required' => $e->getMessage(),
            ]);
        }
    }
}
