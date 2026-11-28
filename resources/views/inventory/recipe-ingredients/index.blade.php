<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Product Ingredients
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @include('inventory._navigation')

                    {{-- Recipe Information --}}
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

                        <div>
                            <h3 class="text-xl font-semibold">
                                {{ $recipe->sellableItem->item_name }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $recipe->recipe_name }}
                            </p>

                            <p class="mt-1 text-sm">
                                Selling Price:
                                ₱{{ number_format($recipe->sellableItem->current_price, 2) }}
                            </p>
                        </div>

                        <a href="{{ route('inventory.recipes.index') }}"
                            class="text-sm text-blue-600 hover:underline">

                            Back to Recipes

                        </a>

                    </div>

                    {{-- Messages --}}
                    @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">

                        @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                        @endforeach

                    </div>
                    @endif

                    {{-- Recipe Status --}}
                    <div class="mb-6 rounded-md border p-4">

                        <div class="flex flex-wrap items-center justify-between gap-4">

                            <div>
                                <span class="text-sm font-medium">
                                    Recipe Status:
                                </span>

                                @if ($recipe->is_active)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">
                                    Active
                                </span>
                                @else
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">
                                    Inactive
                                </span>
                                @endif
                            </div>

                            <form method="POST"
                                action="{{ route('inventory.recipes.update-status', $recipe) }}"
                                onsubmit="return confirm('Change recipe status? Activating this recipe will deactivate other active recipes for this product.')">

                                @csrf
                                @method('PATCH')

                                <input type="hidden"
                                    name="is_active"
                                    value="{{ $recipe->is_active ? 0 : 1 }}">

                                <x-primary-button>
                                    {{ $recipe->is_active ? 'Deactivate Recipe' : 'Activate Recipe' }}
                                </x-primary-button>

                            </form>

                        </div>

                    </div>

                    {{-- Add Ingredient --}}
                    @if (!$recipe->is_active)

                    <div class="mb-8 rounded-lg border p-5">

                        <h3 class="mb-4 text-lg font-semibold">
                            Add Ingredient
                        </h3>

                        <form method="POST"
                            action="{{ route('inventory.recipes.ingredients.store', $recipe) }}"
                            x-data="{
                                    items: @js($inventoryOptions),
                                    selectedItem: @js((string) old('inventory_item_id', '')),
                                    selectedUnit: @js((string) old('unit_id', '')),

                                    get units() {
                                        return this.items.find(
                                            item => String(item.id) === String(this.selectedItem)
                                        )?.units ?? [];
                                    }
                                }">

                            @csrf

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                                {{-- Inventory Item --}}
                                <div>

                                    <x-input-label
                                        for="inventory_item_id"
                                        value="Inventory Item" />

                                    <select
                                        id="inventory_item_id"
                                        name="inventory_item_id"
                                        x-model="selectedItem"
                                        x-on:change="selectedUnit = String(units[0]?.id ?? '')"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        required>

                                        <option value="">
                                            Select Ingredient
                                        </option>

                                        <template x-for="item in items" :key="item.id">

                                            <option
                                                :value="String(item.id)"
                                                x-text="item.name">
                                            </option>

                                        </template>

                                    </select>

                                </div>

                                {{-- Required Quantity --}}
                                <div>

                                    <x-input-label
                                        for="quantity_required"
                                        value="Quantity per Serving" />

                                    <x-text-input
                                        id="quantity_required"
                                        name="quantity_required"
                                        type="number"
                                        min="0.0001"
                                        max="99999999999.9999"
                                        step="0.0001"
                                        class="mt-1 block w-full"
                                        :value="old('quantity_required')"
                                        required />

                                </div>

                                {{-- Measurement Unit --}}
                                <div>

                                    <x-input-label
                                        for="unit_id"
                                        value="Measurement Unit" />

                                    <select
                                        id="unit_id"
                                        name="unit_id"
                                        x-model="selectedUnit"
                                        :disabled="!selectedItem"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        required>

                                        <option value="">
                                            Select Unit
                                        </option>

                                        <template x-for="unit in units" :key="unit.id">

                                            <option
                                                :value="String(unit.id)"
                                                x-text="unit.label">
                                            </option>

                                        </template>

                                    </select>

                                </div>

                            </div>

                            <p class="mt-3 text-xs text-gray-500">
                                Enter the amount consumed when preparing
                                one serving of this product.
                            </p>

                            <div class="mt-4">

                                <x-primary-button>
                                    Add Ingredient
                                </x-primary-button>

                            </div>

                        </form>

                    </div>

                    @else

                    <div class="mb-6 rounded-md bg-blue-50 p-4 text-sm text-blue-700">

                        This recipe is currently active.
                        Deactivate it before modifying ingredients.

                    </div>

                    @endif

                    {{-- Ingredients List --}}
                    <h3 class="mb-4 text-lg font-semibold">
                        Configured Ingredients
                        ({{ $recipe->ingredients->count() }})
                    </h3>

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200 text-sm">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        Ingredient
                                    </th>

                                    <th class="px-4 py-3 text-right">
                                        Required Quantity
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Measurement Unit
                                    </th>

                                    <th class="px-4 py-3 text-right">
                                        Current Stock
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Actions
                                    </th>
                                </tr>

                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @forelse ($recipe->ingredients as $ingredient)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3 font-medium">

                                        {{ $ingredient->inventoryItem->item_name }}

                                        @if (!$ingredient->inventoryItem->is_active)
                                        <p class="text-xs text-red-600">
                                            Inactive inventory item
                                        </p>
                                        @endif

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        {{ rtrim(rtrim($ingredient->quantity_required, '0'), '.') }}

                                    </td>

                                    <td class="px-4 py-3">

                                        {{ $ingredient->unit->abbreviation }}

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        {{ rtrim(rtrim($ingredient->inventoryItem->current_stock, '0'), '.') }}

                                        {{ $ingredient->inventoryItem->baseUnit->abbreviation }}

                                    </td>

                                    <td class="px-4 py-3">

                                        @if (!$recipe->is_active)

                                        <div class="flex items-center gap-3">

                                            <a href="{{ route('inventory.recipes.ingredients.edit', [
                                                            $recipe,
                                                            $ingredient,
                                                        ]) }}"
                                                class="text-blue-600 hover:underline">

                                                Edit

                                            </a>

                                            <form method="POST"
                                                action="{{ route('inventory.recipes.ingredients.destroy', [
                                                            $recipe,
                                                            $ingredient,
                                                        ]) }}"
                                                onsubmit="return confirm('Remove this ingredient from the recipe?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="text-red-600 hover:underline">

                                                    Remove

                                                </button>

                                            </form>

                                        </div>

                                        @else

                                        <span class="text-xs text-gray-500">
                                            Locked
                                        </span>

                                        @endif

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="5"
                                        class="px-4 py-10 text-center text-gray-500">

                                        No ingredients configured yet.

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-6 rounded-md bg-blue-50 p-4 text-sm text-blue-700">

                        Ingredient quantities represent consumption
                        per serving. Inventory will only be deducted
                        during successful POS transactions.

                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>