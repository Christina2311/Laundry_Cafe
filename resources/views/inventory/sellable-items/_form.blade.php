<div class="space-y-6">

    {{-- Parent Menu Item --}}
    <div>

        <x-input-label
            for="menu_item_id"
            value="Parent Menu Item" />

        @isset($item)

        <div class="mt-1 rounded-md border bg-gray-50 p-3 text-sm">
            {{ $item->menuItem->menu_item_name ?? 'Unavailable' }}
        </div>

        <p class="mt-1 text-xs text-gray-500">
            The parent menu item cannot be changed.
        </p>

        @else

        <select
            id="menu_item_id"
            name="menu_item_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            required>

            <option value="">
                Select Menu Item
            </option>

            @foreach ($menuItems as $menuItem)

            <option
                value="{{ $menuItem->menu_item_id }}"
                @selected(old('menu_item_id')==$menuItem->menu_item_id)>

                {{ $menuItem->menu_item_name }}
                — {{ $menuItem->category }}

            </option>

            @endforeach

        </select>

        @endisset

        <x-input-error
            :messages="$errors->get('menu_item_id')"
            class="mt-2" />

    </div>

    {{-- Product Name --}}
    <div>

        <x-input-label
            for="item_name"
            value="Product Name" />

        <x-text-input
            id="item_name"
            name="item_name"
            type="text"
            class="mt-1 block w-full"
            :value="old('item_name', $item->item_name ?? '')"
            placeholder="e.g. Iced Americano - Regular"
            maxlength="150"
            required />

        <x-input-error
            :messages="$errors->get('item_name')"
            class="mt-2" />

    </div>

    {{-- Selling Price --}}
    <div>

        <x-input-label
            for="current_price"
            value="Selling Price (PHP)" />

        <x-text-input
            id="current_price"
            name="current_price"
            type="number"
            step="0.01"
            min="0.01"
            max="9999999999.99"
            class="mt-1 block w-full"
            :value="old('current_price', $item->current_price ?? '')"
            placeholder="0.00"
            required />

        <x-input-error
            :messages="$errors->get('current_price')"
            class="mt-2" />

    </div>

    {{-- Information --}}
    <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">

        This product requires an active and valid recipe
        before it can be purchased through the POS.

        Creating or editing this product will not change
        inventory stock.

    </div>

    <div class="flex items-center gap-4">

        <x-primary-button>
            {{ $buttonText ?? 'Save Product' }}
        </x-primary-button>

        <a href="{{ route('inventory.sellable-items.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>