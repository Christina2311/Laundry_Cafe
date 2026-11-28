<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Recipe Ingredient
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold">
                        {{ $recipe->sellableItem->item_name }}
                    </h3>

                    <p class="mb-6 text-sm text-gray-500">
                        {{ $recipe->recipe_name }}
                    </p>

                    @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">

                        @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                        @endforeach

                    </div>
                    @endif

                    <form method="POST"
                        action="{{ route('inventory.recipes.ingredients.update', [
                            $recipe,
                            $ingredient,
                        ]) }}"
                        class="space-y-6">

                        @csrf
                        @method('PUT')

                        {{-- Ingredient --}}
                        <div>

                            <x-input-label value="Inventory Item" />

                            <div class="mt-1 rounded-md border bg-gray-50 p-3">

                                {{ $ingredient->inventoryItem->item_name }}

                            </div>

                        </div>

                        {{-- Quantity --}}
                        <div>

                            <x-input-label
                                for="quantity_required"
                                value="Quantity per Serving" />

                            <x-text-input
                                id="quantity_required"
                                name="quantity_required"
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                max="99999999999.9999"
                                class="mt-1 block w-full"
                                :value="old('quantity_required', $ingredient->quantity_required)"
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                required>

                                @foreach ($units as $unit)

                                <option
                                    value="{{ $unit['id'] }}"
                                    @selected(old('unit_id', $ingredient->unit_id) == $unit['id'])>

                                    {{ $unit['label'] }}

                                </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="flex items-center gap-4">

                            <x-primary-button>
                                Update Ingredient
                            </x-primary-button>

                            <a href="{{ route('inventory.recipes.ingredients.index', $recipe) }}"
                                class="text-sm text-gray-600 hover:underline">

                                Cancel

                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>