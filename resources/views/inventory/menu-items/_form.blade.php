<div class="space-y-6">

    {{-- Menu Item Name --}}
    <div>
        <x-input-label
            for="menu_item_name"
            value="Menu Item Name" />

        <x-text-input
            id="menu_item_name"
            name="menu_item_name"
            type="text"
            class="mt-1 block w-full"
            :value="old('menu_item_name', $menuItem->menu_item_name ?? '')"
            placeholder="e.g. Iced Latte"
            maxlength="150"
            required />

        <x-input-error
            :messages="$errors->get('menu_item_name')"
            class="mt-2" />
    </div>

    {{-- Category --}}
    <div>
        <x-input-label
            for="category"
            value="Category" />

        <x-text-input
            id="category"
            name="category"
            type="text"
            class="mt-1 block w-full"
            :value="old('category', $menuItem->category ?? '')"
            placeholder="e.g. Coffee, Non-Coffee, Snacks"
            maxlength="100"
            required />

        <x-input-error
            :messages="$errors->get('category')"
            class="mt-2" />
    </div>

    {{-- Description --}}
    <div>
        <x-input-label
            for="description"
            value="Description (Optional)" />

        <textarea
            id="description"
            name="description"
            rows="4"
            maxlength="2000"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            placeholder="Enter a short product description...">{{ old('description', $menuItem->description ?? '') }}</textarea>

        <x-input-error
            :messages="$errors->get('description')"
            class="mt-2" />
    </div>

    <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">
        Product prices, sizes, and ingredient recipes
        will be configured separately under sellable products.
    </div>

    <div class="flex items-center gap-4">

        <x-primary-button>
            {{ $buttonText ?? 'Save Menu Item' }}
        </x-primary-button>

        <a href="{{ route('inventory.menu-items.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>