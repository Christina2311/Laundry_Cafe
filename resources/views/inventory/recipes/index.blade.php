<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Recipe Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @include('inventory._navigation')

                    {{-- Header --}}
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold">
                                Café Product Recipes
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Manage recipe configurations for automatic
                                ingredient consumption.
                            </p>
                        </div>

                        <a href="{{ route('inventory.recipes.create', [
                                'item_id' => request('item_id'),
                            ]) }}"
                            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            + Add Recipe
                        </a>

                    </div>

                    {{-- Success Message --}}
                    @if (session('success'))

                    <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                        {{ session('success') }}
                    </div>

                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())

                    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">

                        @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                        @endforeach

                    </div>

                    @endif

                    {{-- Filters --}}
                    <form method="GET"
                        action="{{ route('inventory.recipes.index') }}"
                        class="mb-6">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                            <div>

                                <x-input-label
                                    for="search"
                                    value="Search" />

                                <x-text-input
                                    id="search"
                                    name="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="request('search')"
                                    placeholder="Search recipe or product..." />

                            </div>

                            <div>

                                <x-input-label
                                    for="item_id"
                                    value="Café Product" />

                                <select
                                    id="item_id"
                                    name="item_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Products
                                    </option>

                                    @foreach ($products as $product)

                                    <option
                                        value="{{ $product->item_id }}"
                                        @selected(request('item_id')==$product->item_id)>

                                        {{ $product->item_name }}

                                    </option>

                                    @endforeach

                                </select>

                            </div>

                            <div>

                                <x-input-label
                                    for="status"
                                    value="Recipe Status" />

                                <select
                                    id="status"
                                    name="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Statuses
                                    </option>

                                    <option value="active"
                                        @selected(request('status')==='active' )>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        @selected(request('status')==='inactive' )>
                                        Inactive
                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="mt-4 flex items-center gap-3">

                            <x-primary-button>
                                Apply Filters
                            </x-primary-button>

                            <a href="{{ route('inventory.recipes.index') }}"
                                class="text-sm text-gray-600 hover:underline">
                                Reset Filters
                            </a>

                        </div>

                    </form>

                    <p class="mb-3 text-sm text-gray-500">
                        {{ $recipes->total() }} recipe(s) found.
                    </p>

                    {{-- Recipes Table --}}
                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200 text-sm">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        Recipe Name
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Café Product
                                    </th>

                                    <th class="px-4 py-3 text-center">
                                        Ingredients
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Status
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Actions
                                    </th>
                                </tr>

                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @forelse ($recipes as $recipe)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3 font-medium">
                                        {{ $recipe->recipe_name }}
                                    </td>

                                    <td class="px-4 py-3">

                                        {{ $recipe->sellableItem->item_name }}

                                        @if (
                                        !$recipe->sellableItem->is_active
                                        || !$recipe->sellableItem->menuItem?->is_active
                                        )

                                        <p class="mt-1 text-xs text-red-600">
                                            Product unavailable
                                        </p>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3 text-center">

                                        {{ $recipe->ingredients_count }}

                                        @if ($recipe->ingredients_count === 0)

                                        <p class="mt-1 text-xs text-yellow-700">
                                            Not configured
                                        </p>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3">

                                        @if ($recipe->is_active)

                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                            Active
                                        </span>

                                        @else

                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                            Inactive
                                        </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="flex items-center gap-3">

                                            <a href="{{ route('inventory.recipes.edit', $recipe) }}"
                                                class="font-medium text-blue-600 hover:underline">
                                                Edit
                                            </a>
                                            <a href="{{ route('inventory.recipes.ingredients.index', $recipe) }}"
                                                class="font-medium text-blue-600 hover:underline">

                                                Ingredients

                                            </a>

                                            <form method="POST"
                                                action="{{ route('inventory.recipes.update-status', $recipe) }}"
                                                onsubmit="return confirm('Change recipe status? Activating this recipe will deactivate any other active recipe for this product.')">

                                                @csrf
                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="is_active"
                                                    value="{{ $recipe->is_active ? 0 : 1 }}">

                                                <button type="submit"
                                                    @class([ 'text-sm font-medium hover:underline' , 'text-red-600'=> $recipe->is_active,
                                                    'text-green-600' => !$recipe->is_active,
                                                    ])>

                                                    {{ $recipe->is_active ? 'Deactivate' : 'Activate' }}

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="5"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No recipes found.
                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-6">
                        {{ $recipes->links() }}
                    </div>

                    <div class="mt-6 rounded-md bg-blue-50 p-4 text-sm text-blue-700">

                        Only one active recipe is allowed per café product.
                        Recipes must contain valid ingredients before
                        activation. Recipe configuration does not
                        deduct inventory stock.

                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>