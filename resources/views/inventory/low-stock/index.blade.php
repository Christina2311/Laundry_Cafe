<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Low Stock Monitoring
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @include('inventory._navigation')

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">
                            Inventory Stock Overview
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Monitor stock availability and identify
                            supplies that require replenishment.
                        </p>
                    </div>

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif

                    {{-- Summary Cards --}}
                    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                        <a href="{{ route('inventory.low-stock.index') }}"
                            class="rounded-lg border bg-gray-50 p-5">

                            <p class="text-sm text-gray-600">
                                Active Items
                            </p>

                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                {{ $summary['total'] }}
                            </p>
                        </a>

                        <a href="{{ route('inventory.low-stock.index', ['stock_status' => 'out']) }}"
                            class="rounded-lg border border-red-200 bg-red-50 p-5">

                            <p class="text-sm text-red-700">
                                Out of Stock
                            </p>

                            <p class="mt-2 text-3xl font-bold text-red-700">
                                {{ $summary['out'] }}
                            </p>
                        </a>

                        <a href="{{ route('inventory.low-stock.index', ['stock_status' => 'low']) }}"
                            class="rounded-lg border border-yellow-200 bg-yellow-50 p-5">

                            <p class="text-sm text-yellow-700">
                                Low Stock
                            </p>

                            <p class="mt-2 text-3xl font-bold text-yellow-700">
                                {{ $summary['low'] }}
                            </p>
                        </a>

                        <a href="{{ route('inventory.low-stock.index', ['stock_status' => 'sufficient']) }}"
                            class="rounded-lg border border-green-200 bg-green-50 p-5">

                            <p class="text-sm text-green-700">
                                Sufficient Stock
                            </p>

                            <p class="mt-2 text-3xl font-bold text-green-700">
                                {{ $summary['sufficient'] }}
                            </p>
                        </a>

                    </div>

                    {{-- Filters --}}
                    <form method="GET"
                        action="{{ route('inventory.low-stock.index') }}"
                        class="mb-6">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                            <div>
                                <x-input-label
                                    for="search"
                                    value="Inventory Item" />

                                <x-text-input
                                    id="search"
                                    name="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Search inventory item..."
                                    :value="request('search')" />
                            </div>

                            <div>
                                <x-input-label
                                    for="category_id"
                                    value="Category" />

                                <select
                                    id="category_id"
                                    name="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Categories
                                    </option>

                                    @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->category_id }}"
                                        @selected(request('category_id')==$category->category_id)>

                                        {{ $category->category_name }}

                                    </option>
                                    @endforeach

                                </select>
                            </div>

                            <div>
                                <x-input-label
                                    for="stock_status"
                                    value="Stock Status" />

                                <select
                                    id="stock_status"
                                    name="stock_status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Stock Statuses
                                    </option>

                                    <option value="out"
                                        @selected(request('stock_status')==='out' )>
                                        Out of Stock
                                    </option>

                                    <option value="low"
                                        @selected(request('stock_status')==='low' )>
                                        Low Stock
                                    </option>

                                    <option value="sufficient"
                                        @selected(request('stock_status')==='sufficient' )>
                                        Sufficient Stock
                                    </option>

                                </select>
                            </div>

                        </div>

                        <div class="mt-4 flex items-center gap-3">

                            <x-primary-button>
                                Apply Filters
                            </x-primary-button>

                            <a href="{{ route('inventory.low-stock.index') }}"
                                class="text-sm text-gray-600 hover:underline">
                                Reset Filters
                            </a>

                        </div>

                    </form>

                    <p class="mb-3 text-sm text-gray-500">
                        {{ $items->total() }} inventory item(s) found.
                    </p>

                    {{-- Inventory Table --}}
                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200 text-sm">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        Inventory Item
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Category
                                    </th>

                                    <th class="px-4 py-3 text-right">
                                        Current Stock
                                    </th>

                                    <th class="px-4 py-3 text-right">
                                        Reorder Level
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Stock Status
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @forelse ($items as $item)

                                @php
                                $stock = rtrim(
                                rtrim((string) $item->current_stock, '0'),
                                '.'
                                );

                                $reorder = rtrim(
                                rtrim((string) $item->reorder_level, '0'),
                                '.'
                                );

                                $unit = $item->baseUnit->abbreviation;
                                @endphp

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3 font-medium">
                                        {{ $item->item_name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $item->category->category_name }}
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">
                                        {{ $stock }} {{ $unit }}
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        {{ $reorder }} {{ $unit }}
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($item->current_stock == 0)

                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                            Out of Stock
                                        </span>

                                        @elseif ($item->isLowStock())

                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">
                                            Low Stock
                                        </span>

                                        @else

                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                            Sufficient Stock
                                        </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3">

                                        <a href="{{ route('inventory.items.edit', $item) }}"
                                            class="font-medium text-blue-600 hover:underline">
                                            Edit Item
                                        </a>

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="6"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No matching inventory items found.
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

                        Stock alerts are based on each inventory
                        item's configured reorder level.
                        Inactive inventory items are excluded
                        from monitoring.

                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>