@php
$isEditing = isset($item);
@endphp

<div class="space-y-6">

    {{-- Item Name --}}
    <div>

        <x-input-label
            for="item_name"
            value="Inventory Item Name" />

        <x-text-input
            id="item_name"
            name="item_name"
            type="text"
            class="mt-1 block w-full"
            :value="old('item_name', $item->item_name ?? '')"
            placeholder="e.g. Detergent Powder"
            maxlength="150"
            required
            autofocus />

        <x-input-error
            :messages="$errors->get('item_name')"
            class="mt-2" />

    </div>

    {{-- Category --}}
    <div>

        <x-input-label
            for="category_id"
            value="Inventory Category" />

        <select
            id="category_id"
            name="category_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            required>

            <option value="">Select Category</option>

            @foreach ($categories as $category)

            <option value="{{ $category->category_id }}"
                @selected(old('category_id', $item->category_id ?? '') == $category->category_id)>

                {{ $category->category_name }}

            </option>

            @endforeach

        </select>

        <x-input-error
            :messages="$errors->get('category_id')"
            class="mt-2" />

    </div>

    {{-- Base Measurement Unit --}}
    <div>

        <x-input-label
            for="base_unit_id"
            value="Base Measurement Unit" />

        <select
            id="base_unit_id"
            name="base_unit_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            required>

            <option value="">Select Base Unit</option>

            @foreach ($units as $unit)

            <option value="{{ $unit->unit_id }}"
                @selected(old('base_unit_id', $item->base_unit_id ?? '') == $unit->unit_id)>

                {{ $unit->unit_name }}
                ({{ $unit->abbreviation }})

            </option>

            @endforeach

        </select>

        <p class="mt-1 text-xs text-gray-500">
            The standard unit used to track this item's stock.
            Changing the base unit is restricted once the item is in use.
        </p>

        <x-input-error
            :messages="$errors->get('base_unit_id')"
            class="mt-2" />

    </div>

    {{-- Reorder Level --}}
    <div>

        <x-input-label
            for="reorder_level"
            value="Reorder Level" />

        <x-text-input
            id="reorder_level"
            name="reorder_level"
            type="number"
            min="0"
            max="99999999999.9999"
            step="0.0001"
            class="mt-1 block w-full"
            :value="old('reorder_level', $item->reorder_level ?? 0)"
            required />

        <p class="mt-1 text-xs text-gray-500">
            The item will be marked as low stock when its quantity
            reaches or falls below this level, in its base unit.
        </p>

        <x-input-error
            :messages="$errors->get('reorder_level')"
            class="mt-2" />

    </div>

    {{-- Current Stock Information --}}
    <div class="rounded-md border border-blue-200 bg-blue-50 p-4">

        <p class="text-sm font-semibold text-blue-800">
            Current Stock
        </p>

        @if ($isEditing)

        <p class="mt-1 text-lg font-semibold text-blue-900">
            {{ rtrim(rtrim($item->current_stock, '0'), '.') }}
            {{ $item->baseUnit->abbreviation }}
        </p>

        @else

        <p class="mt-1 text-lg font-semibold text-blue-900">
            0
        </p>

        @endif

        <p class="mt-2 text-xs text-blue-700">
            Stock quantities cannot be manually edited.
            Stock is updated through authorized inventory transactions.
        </p>

    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-4">

        <x-primary-button>
            {{ $isEditing ? 'Save Changes' : 'Create Inventory Item' }}
        </x-primary-button>

        <a href="{{ route('inventory.items.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>