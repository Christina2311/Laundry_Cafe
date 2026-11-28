@php
$isEditing = isset($conversion);

$itemOptions = $isEditing
? collect([$conversion->inventoryItem])
: $items;

$unitOptions = $isEditing
? collect([$conversion->fromUnit])
: $units;

$itemOptions->each(fn ($item) => $item->loadMissing('baseUnit'));

$itemData = $itemOptions->mapWithKeys(fn ($item) => [
$item->inventory_item_id => [
'name' => $item->item_name,
'baseUnitId' => (string) $item->base_unit_id,
'baseUnit' => $item->baseUnit->abbreviation,
],
]);

$unitData = $unitOptions->mapWithKeys(fn ($unit) => [
$unit->unit_id => $unit->abbreviation,
]);
@endphp

<div class="space-y-6"
    x-data="{
        items: @js($itemData),
        units: @js($unitData),

        itemId: @js((string) ($isEditing
            ? $conversion->inventory_item_id
            : old('inventory_item_id', ''))),

        fromUnitId: @js((string) ($isEditing
            ? $conversion->from_unit_id
            : old('from_unit_id', ''))),

        factor: @js((string) old(
            'conversion_factor',
            $conversion->conversion_factor ?? ''
        )),

        get baseUnit() {
            return this.items[this.itemId]?.baseUnit || '';
        },

        get sourceUnit() {
            return this.units[this.fromUnitId] || '';
        },

        get sameUnit() {
            return this.itemId
                && this.fromUnitId
                && this.items[this.itemId]?.baseUnitId === this.fromUnitId;
        }
    }">

    {{-- Inventory Item --}}
    <div>

        <x-input-label
            for="inventory_item_id"
            value="Inventory Item" />

        @if ($isEditing)

        <div class="mt-1 rounded-md border bg-gray-100 p-3">
            {{ $conversion->inventoryItem->item_name }}
        </div>

        @else

        <select
            id="inventory_item_id"
            name="inventory_item_id"
            x-model="itemId"
            class="mt-1 block w-full rounded-md border-gray-300"
            required>

            <option value="">Select Inventory Item</option>

            @foreach ($items as $item)

            <option value="{{ $item->inventory_item_id }}">
                {{ $item->item_name }}
                ({{ $item->baseUnit->abbreviation }})
            </option>

            @endforeach

        </select>

        @endif

        <x-input-error
            :messages="$errors->get('inventory_item_id')"
            class="mt-2" />

    </div>

    {{-- Source Unit --}}
    <div>

        <x-input-label
            for="from_unit_id"
            value="From Measurement Unit" />

        @if ($isEditing)

        <div class="mt-1 rounded-md border bg-gray-100 p-3">
            {{ $conversion->fromUnit->unit_name }}
            ({{ $conversion->fromUnit->abbreviation }})
        </div>

        @else

        <select
            id="from_unit_id"
            name="from_unit_id"
            x-model="fromUnitId"
            class="mt-1 block w-full rounded-md border-gray-300"
            required>

            <option value="">Select Source Unit</option>

            @foreach ($units as $unit)

            <option value="{{ $unit->unit_id }}">
                {{ $unit->unit_name }}
                ({{ $unit->abbreviation }})
            </option>

            @endforeach

        </select>

        @endif

        <p class="mt-1 text-xs text-gray-500">
            The unit used when purchasing or measuring the item.
        </p>

        <x-input-error
            :messages="$errors->get('from_unit_id')"
            class="mt-2" />

        <p x-show="sameUnit"
            class="mt-2 text-sm text-red-600">
            The source unit cannot be the same as the base unit.
        </p>

    </div>

    {{-- Destination Base Unit --}}
    <div>

        <x-input-label value="Destination Base Unit" />

        <div class="mt-1 rounded-md border bg-gray-100 p-3">

            <span x-text="baseUnit || 'Select an inventory item first'">
            </span>

        </div>

        <p class="mt-1 text-xs text-gray-500">
            Automatically determined from the selected inventory item.
        </p>

    </div>

    {{-- Conversion Factor --}}
    <div>

        <x-input-label
            for="conversion_factor"
            value="Conversion Factor" />

        <x-text-input
            id="conversion_factor"
            name="conversion_factor"
            type="number"
            min="0.00000001"
            max="9999999999.99999999"
            step="0.00000001"
            x-model="factor"
            class="mt-1 block w-full"
            placeholder="e.g. 5000"
            required />

        <p class="mt-1 text-xs text-gray-500">
            The quantity of base units contained in one source unit.
        </p>

        <x-input-error
            :messages="$errors->get('conversion_factor')"
            class="mt-2" />

    </div>

    {{-- Live Conversion Preview --}}
    <div class="rounded-md border border-blue-200 bg-blue-50 p-4">

        <p class="text-sm font-semibold text-blue-800">
            Conversion Preview
        </p>

        <div x-show="itemId && fromUnitId && Number(factor) > 0 && !sameUnit"
            class="mt-2 text-lg font-semibold text-blue-900">

            1 <span x-text="sourceUnit"></span>
            =
            <span x-text="factor"></span>
            <span x-text="baseUnit"></span>

        </div>

        <p x-show="!itemId || !fromUnitId || !(Number(factor) > 0) || sameUnit"
            class="mt-2 text-sm text-blue-700">

            Complete the fields to preview the conversion.

        </p>

        <p class="mt-2 text-xs text-blue-700">
            Creating a conversion does not increase or decrease inventory stock.
        </p>

    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-4">

        <x-primary-button
            x-bind:disabled="sameUnit"
            class="disabled:opacity-50">

            {{ $isEditing ? 'Save Changes' : 'Create Conversion' }}

        </x-primary-button>

        <a href="{{ route('inventory.conversions.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>