@php
$isEditing = isset($category);
@endphp

<div class="space-y-6">

    {{-- Category Name --}}
    <div>

        <x-input-label
            for="category_name"
            value="Category Name" />

        <x-text-input
            id="category_name"
            name="category_name"
            type="text"
            class="mt-1 block w-full"
            :value="old('category_name', $category->category_name ?? '')"
            maxlength="100"
            required
            autofocus />

        <x-input-error
            :messages="$errors->get('category_name')"
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
            maxlength="1000"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $category->description ?? '') }}</textarea>

        <x-input-error
            :messages="$errors->get('description')"
            class="mt-2" />

    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-4">

        <x-primary-button>
            {{ $isEditing ? 'Save Changes' : 'Create Category' }}
        </x-primary-button>

        <a href="{{ route('inventory.categories.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>