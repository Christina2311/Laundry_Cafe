<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Inventory Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
            <div class="mb-4 rounded bg-green-100 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 p-4 text-red-700">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

                    <div>
                        <h3 class="text-lg font-semibold">
                            Inventory Items
                        </h3>

                        <p class="text-sm text-gray-500">
                            Manage cafe ingredients and laundry supplies.
                        </p>
                    </div>

                    <a href="{{ route('inventory.items.create') }}"
                        class="rounded bg-gray-800 px-4 py-2 text-white">
                        + Add Inventory Item
                    </a>

                </div>

                @include('inventory._navigation')

                {{-- Search and Filters --}}
                <form method="GET"
                    action="{{ route('inventory.items.index') }}"
                    class="mb-6 flex flex-wrap items-center gap-3">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search inventory..."
                        class="rounded-md border-gray-300">

                    {{-- Category Filter --}}
                    <select name="category_id"
                        class="rounded-md border-gray-300">

                        <option value="">All Categories</option>

                        @foreach ($categories as $category)

                        <option value="{{ $category->category_id }}"
                            @selected(request('category_id')==$category->category_id)>

                            {{ $category->category_name }}

                        </option>

                        @endforeach

                    </select>

                    {{-- Status Filter --}}
                    <select name="status"
                        class="rounded-md border-gray-300">

                        <option value="">All Statuses</option>

                        <option value="1"
                            @selected(request('status')==='1' )>
                            Active
                        </option>

                        <option value="0"
                            @selected(request('status')==='0' )>
                            Inactive
                        </option>

                    </select>

                    {{-- Stock Filter --}}
                    <select name="stock"
                        class="rounded-md border-gray-300">

                        <option value="">All Stock Levels</option>

                        <option value="low"
                            @selected(request('stock')==='low' )>
                            Low Stock
                        </option>

                        <option value="available"
                            @selected(request('stock')==='available' )>
                            Above Reorder Level
                        </option>

                    </select>

                    <x-primary-button>
                        Filter
                    </x-primary-button>

                    <a href="{{ route('inventory.items.index') }}"
                        class="text-sm text-gray-600 hover:underline">
                        Reset
                    </a>

                </form>

                {{-- Inventory Table --}}
                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Item Name</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Current Stock</th>
                                <th class="p-3">Reorder Level</th>
                                <th class="p-3">Stock Level</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($items as $item)

                            <tr class="border-b">

                                <td class="p-3 font-medium">
                                    {{ $item->item_name }}
                                </td>

                                <td class="p-3">
                                    {{ $item->category->category_name }}
                                </td>

                                <td class="p-3">
                                    {{ rtrim(rtrim($item->current_stock, '0'), '.') }}
                                    {{ $item->baseUnit->abbreviation }}
                                </td>

                                <td class="p-3">
                                    {{ rtrim(rtrim($item->reorder_level, '0'), '.') }}
                                    {{ $item->baseUnit->abbreviation }}
                                </td>

                                {{-- Stock Level --}}
                                <td class="p-3">

                                    @if ($item->current_stock == 0)

                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs text-red-700">
                                        Out of Stock
                                    </span>

                                    @elseif ($item->isLowStock())

                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs text-yellow-700">
                                        Low Stock
                                    </span>

                                    @else

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">
                                        Sufficient
                                    </span>

                                    @endif

                                </td>

                                {{-- Active Status --}}
                                <td class="p-3">

                                    @if ($item->is_active)

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">
                                        Active
                                    </span>

                                    @else

                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">
                                        Inactive
                                    </span>

                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td class="p-3">

                                    <div class="flex items-center gap-3">

                                        <a href="{{ route('inventory.items.edit', $item) }}"
                                            class="text-blue-600 hover:underline">
                                            Edit
                                        </a>

                                        @if (!$item->is_active || $item->current_stock == 0)

                                        <form method="POST"
                                            action="{{ route('inventory.items.update-status', $item) }}">

                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden"
                                                name="is_active"
                                                value="{{ $item->is_active ? 0 : 1 }}">

                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to change this item status?')"
                                                @class([ 'hover:underline' , 'text-red-600'=> $item->is_active,
                                                'text-green-600' => !$item->is_active,
                                                ])>

                                                {{ $item->is_active ? 'Deactivate' : 'Activate' }}

                                            </button>

                                        </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="7"
                                    class="p-6 text-center text-gray-500">
                                    No inventory items found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-6">
                    {{ $items->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>