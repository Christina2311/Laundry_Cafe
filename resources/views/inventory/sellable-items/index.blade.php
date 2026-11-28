<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sellable Product Management
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
                                Café Products
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Manage product variants, selling prices,
                                and recipe configuration status.
                            </p>
                        </div>

                        <a href="{{ route('inventory.sellable-items.create') }}"
                            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            + Add Product
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
                        action="{{ route('inventory.sellable-items.index') }}"
                        class="mb-6">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                            <div>
                                <x-input-label
                                    for="search"
                                    value="Product Name" />

                                <x-text-input
                                    id="search"
                                    name="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="request('search')"
                                    placeholder="Search products..." />
                            </div>

                            <div>
                                <x-input-label
                                    for="menu_item_id"
                                    value="Menu Item" />

                                <select
                                    id="menu_item_id"
                                    name="menu_item_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Menu Items
                                    </option>

                                    @foreach ($menuItems as $menuItem)

                                    <option
                                        value="{{ $menuItem->menu_item_id }}"
                                        @selected(request('menu_item_id')==$menuItem->menu_item_id)>

                                        {{ $menuItem->menu_item_name }}

                                    </option>

                                    @endforeach

                                </select>
                            </div>

                            <div>
                                <x-input-label
                                    for="status"
                                    value="Product Status" />

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

                            <a href="{{ route('inventory.sellable-items.index') }}"
                                class="text-sm text-gray-600 hover:underline">
                                Reset Filters
                            </a>

                        </div>

                    </form>

                    <p class="mb-3 text-sm text-gray-500">
                        {{ $items->total() }} café product(s) found.
                    </p>

                    {{-- Products Table --}}
                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200 text-sm">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        Product
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Menu Item
                                    </th>

                                    <th class="px-4 py-3 text-right">
                                        Price
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Recipe
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

                                @forelse ($items as $item)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3 font-medium">
                                        {{ $item->item_name }}
                                    </td>

                                    <td class="px-4 py-3">

                                        {{ $item->menuItem->menu_item_name ?? '—' }}

                                        @if (!$item->menuItem?->is_active)

                                        <p class="mt-1 text-xs text-red-600">
                                            Parent unavailable
                                        </p>

                                        @endif

                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">
                                        ₱{{ number_format($item->current_price, 2) }}
                                    </td>

                                    {{-- Recipe Status --}}
                                    <td class="px-4 py-3">

                                        @if ($item->active_recipes_count > 1)

                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                            Multiple Active Recipes
                                        </span>

                                        @elseif (
                                        $item->active_recipes_count === 1
                                        && $item->configured_recipes_count === 1
                                        )

                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                            Configured
                                        </span>

                                        @else

                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">
                                            Not Configured
                                        </span>

                                        @endif

                                    </td>

                                    {{-- Product Status --}}
                                    <td class="px-4 py-3">

                                        @if ($item->is_active)

                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                            Active
                                        </span>

                                        @else

                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                            Inactive
                                        </span>

                                        @endif

                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-3">

                                        <div class="flex items-center gap-3">

                                            <a href="{{ route('inventory.sellable-items.edit', $item) }}"
                                                class="font-medium text-blue-600 hover:underline">
                                                Edit
                                            </a>

                                            <a href="{{ route('inventory.recipes.index', [
                                                    'item_id' => $item->item_id,
                                                ]) }}"
                                                class="font-medium text-blue-600 hover:underline">

                                                Recipes

                                            </a>

                                            <form method="POST"
                                                action="{{ route('inventory.sellable-items.update-status', $item) }}"
                                                onsubmit="return confirm('Change this product status?')">

                                                @csrf
                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="is_active"
                                                    value="{{ $item->is_active ? 0 : 1 }}">

                                                <button type="submit"
                                                    @class([ 'text-sm font-medium hover:underline' , 'text-red-600'=> $item->is_active,
                                                    'text-green-600' => !$item->is_active,
                                                    ])>

                                                    {{ $item->is_active ? 'Deactivate' : 'Activate' }}

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="6"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No café products found.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-6">
                        {{ $items->links() }}
                    </div>

                    <div class="mt-6 rounded-md bg-blue-50 p-4 text-sm text-blue-700">

                        Active product status does not guarantee POS
                        availability. The parent menu item must be active,
                        the recipe must be valid, and sufficient inventory
                        must be available.

                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>