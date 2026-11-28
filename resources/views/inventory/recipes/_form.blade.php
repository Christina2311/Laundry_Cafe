<div class="space-y-6">

    {{-- Sellable Product --}}
    <div>

        <x-input-label
            for="item_id"
            value="Café Product" />

        @isset($recipe)

        <div class="mt-1 rounded-md border bg-gray-50 p-3 text-sm">
            {{ $recipe->sellableItem->item_name }}
        </div>

        <p class="mt-1 text-xs text-gray-500">
            The associated product cannot be changed.
        </p>

        @else

        <select
            id="item_id"
            name="item_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            required>

            <option value="">
                Select Café Product
            </option>

            @foreach ($products as $product)

            <option
                value="{{ $product->item_id }}"
                @selected(old('item_id', request('item_id'))==$product->item_id)>

                {{ $product->item_name }}

                {{ !$product->is_active ? '(Inactive)' : '' }}

            </option>

            @endforeach

        </select>

        @endisset

        <x-input-error
            :messages="$errors->get('item_id')"
            class="mt-2" />

    </div>

    {{-- Recipe Name --}}
    <div>

        <x-input-label
            for="recipe_name"
            value="Recipe Name" />

        <x-text-input
            id="recipe_name"
            name="recipe_name"
            type="text"
            class="mt-1 block w-full"
            :value="old('recipe_name', $recipe->recipe_name ?? '')"
            placeholder="e.g. Standard Recipe"
            maxlength="150"
            required />

        <x-input-error
            :messages="$errors->get('recipe_name')"
            class="mt-2" />

    </div>

    <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">

        New recipes are inactive by default.

        Configure the required ingredients before activating
        a recipe. Only one recipe can be active per product.

    </div>

    <div class="flex items-center gap-4">

        <x-primary-button>
            {{ $buttonText ?? 'Save Recipe' }}
        </x-primary-button>

        <a href="{{ route('inventory.recipes.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>