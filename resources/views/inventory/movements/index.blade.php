<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stock Movement History
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @include('inventory._navigation')

                    {{-- Page Description --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">
                            Inventory Transactions
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Monitor incoming and outgoing stock
                            for café and laundry operations.
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

                    {{-- Filters --}}
                    <form method="GET"
                        action="{{ route('inventory.movements.index') }}"
                        class="mb-6">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                            {{-- Search --}}
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

                            {{-- Category --}}
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

                            {{-- Movement Type --}}
                            <div>
                                <x-input-label
                                    for="movement_type"
                                    value="Movement Type" />

                                <select
                                    id="movement_type"
                                    name="movement_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Movement Types
                                    </option>

                                    <option value="in"
                                        @selected(request('movement_type')==='in' )>
                                        Stock In
                                    </option>

                                    <option value="out"
                                        @selected(request('movement_type')==='out' )>
                                        Stock Out
                                    </option>

                                </select>
                            </div>

                            {{-- Movement Reason --}}
                            <div>
                                <x-input-label
                                    for="movement_reason"
                                    value="Movement Reason" />

                                <select
                                    id="movement_reason"
                                    name="movement_reason"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Reasons
                                    </option>

                                    @foreach ($reasons as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(request('movement_reason')===$value)>

                                        {{ $label }}

                                    </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Starting Date --}}
                            <div>
                                <x-input-label
                                    for="date_from"
                                    value="From Date" />

                                <x-text-input
                                    id="date_from"
                                    name="date_from"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :value="request('date_from')" />
                            </div>

                            {{-- Ending Date --}}
                            <div>
                                <x-input-label
                                    for="date_to"
                                    value="To Date" />

                                <x-text-input
                                    id="date_to"
                                    name="date_to"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :value="request('date_to')" />
                            </div>

                        </div>

                        <div class="mt-4 flex items-center gap-3">

                            <x-primary-button>
                                Apply Filters
                            </x-primary-button>

                            <a href="{{ route('inventory.movements.index') }}"
                                class="text-sm text-gray-600 hover:underline">
                                Reset Filters
                            </a>

                        </div>

                    </form>

                    {{-- Record Count --}}
                    <div class="mb-3 text-sm text-gray-500">
                        {{ $movements->total() }} movement(s) found.
                    </div>

                    {{-- Stock Movement Table --}}
                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200 text-sm">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">
                                        Date & Time
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold">
                                        Inventory Item
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold">
                                        Type
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold">
                                        Reason
                                    </th>

                                    <th class="px-4 py-3 text-right font-semibold">
                                        Quantity
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold">
                                        Reference
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold">
                                        Recorded By
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold">
                                        Remarks
                                    </th>
                                </tr>

                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @forelse ($movements as $movement)

                                @php
                                $quantity = rtrim(
                                rtrim(
                                (string) $movement->quantity_change,
                                '0'
                                ),
                                '.'
                                );

                                $isStockIn =
                                $movement->movement_type === 'in';
                                @endphp

                                <tr class="hover:bg-gray-50">

                                    {{-- Date --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <div>
                                            {{ $movement->movement_date->format('M d, Y') }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $movement->movement_date->format('h:i A') }}
                                        </div>

                                    </td>

                                    {{-- Inventory Item --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $movement->inventoryItem?->item_name ?? 'Unknown Item' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $movement->inventoryItem?->category?->category_name ?? '—' }}
                                        </div>

                                    </td>

                                    {{-- Movement Type --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($isStockIn)

                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                            Stock In
                                        </span>

                                        @else

                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                            Stock Out
                                        </span>

                                        @endif

                                    </td>

                                    {{-- Movement Reason --}}
                                    <td class="px-4 py-3">
                                        {{ $reasons[$movement->movement_reason] ?? $movement->movement_reason }}
                                    </td>

                                    {{-- Quantity --}}
                                    <td @class([ 'whitespace-nowrap px-4 py-3 text-right font-semibold' , 'text-green-600'=> $isStockIn,
                                        'text-red-600' => !$isStockIn,
                                        ])>

                                        {{ $isStockIn ? '+' : '' }}{{ $quantity }}

                                        {{ $movement->inventoryItem?->baseUnit?->abbreviation }}

                                    </td>

                                    {{-- Transaction Reference --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($movement->purchase_detail_id)

                                        Purchase #{{ $movement->purchaseDetail?->purchase_id ?? '—' }}

                                        <div class="text-xs text-gray-500">
                                            Detail #{{ $movement->purchase_detail_id }}
                                        </div>

                                        @elseif ($movement->order_detail_id)

                                        Order #{{ $movement->orderDetail?->order_id ?? '—' }}

                                        <div class="text-xs text-gray-500">
                                            Detail #{{ $movement->order_detail_id }}
                                        </div>

                                        @elseif ($movement->laundry_order_id)

                                        Laundry #{{ $movement->laundry_order_id }}

                                        @else

                                        —

                                        @endif

                                    </td>

                                    {{-- Recorded By --}}
                                    <td class="px-4 py-3">
                                        {{ $movement->recordedBy?->name ?? 'Unknown User' }}
                                    </td>

                                    {{-- Remarks --}}
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $movement->remarks ?? '—' }}
                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="8"
                                        class="px-4 py-10 text-center text-gray-500">

                                        No stock movements found.

                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $movements->links() }}
                    </div>

                    {{-- Information --}}
                    <div class="mt-6 rounded-md bg-blue-50 p-4 text-sm text-blue-700">

                        Stock movements are automatically recorded
                        when inventory transactions are processed.
                        These records cannot be manually edited
                        or deleted.

                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>