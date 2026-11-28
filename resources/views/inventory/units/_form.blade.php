@php
$isEditing = isset($unit);
@endphp

<div class="space-y-6">

    {{-- Unit Name --}}
    <div>

        <x-input-label
            for="unit_name"
            value="Unit Name" />

        <x-text-input
            id="unit_name"
            name="unit_name"
            type="text"
            class="mt-1 block w-full"
            :value="old('unit_name', $unit->unit_name ?? '')"
            maxlength="50"
            placeholder="e.g. Kilogram"
            required
            autofocus />

        <x-input-error
            :messages="$errors->get('unit_name')"
            class="mt-2" />

    </div>

    {{-- Abbreviation --}}
    <div>

        <x-input-label
            for="abbreviation"
            value="Abbreviation" />

        <x-text-input
            id="abbreviation"
            name="abbreviation"
            type="text"
            class="mt-1 block w-full"
            :value="old('abbreviation', $unit->abbreviation ?? '')"
            maxlength="20"
            placeholder="e.g. kg"
            required />

        <x-input-error
            :messages="$errors->get('abbreviation')"
            class="mt-2" />

    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-4">

        <x-primary-button>
            {{ $isEditing ? 'Save Changes' : 'Create Unit' }}
        </x-primary-button>

        <a href="{{ route('inventory.units.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>