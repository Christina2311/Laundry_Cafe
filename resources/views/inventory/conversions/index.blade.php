<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Unit Conversions
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
                            Unit Conversions
                        </h3>

                        <p class="text-sm text-gray-500">
                            Configure item-specific measurement conversions.
                        </p>
                    </div>

                    <a href="{{ route('inventory.conversions.create') }}"
                        class="rounded bg-gray-800 px-4 py-2 text-white">
                        + Add Conversion
                    </a>

                </div>

                @include('inventory._navigation')

                {{-- Search and Filters --}}
                <form method="GET"
                    action="{{ route('inventory.conversions.index') }}"
                    class="mb-6 flex flex-wrap items-center gap-3">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search inventory item..."
                        class="rounded-md border-gray-300">

                    <select name="inventory_item_id"
                        class="rounded-md border-gray-300">

                        <option value="">All Inventory Items</option>

                        @foreach ($items as $item)

                        <option value="{{ $item->inventory_item_id }}"
                            @selected(request('inventory_item_id')==$item->inventory_item_id)>

                            {{ $item->item_name }}

                        </option>

                        @endforeach

                    </select>

                    <x-primary-button>
                        Filter
                    </x-primary-button>

                    <a href="{{ route('inventory.conversions.index') }}"
                        class="text-sm text-gray-600 hover:underline">
                        Reset
                    </a>

                </form>

                {{-- Conversions Table --}}
                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Inventory Item</th>
                                <th class="p-3">From Unit</th>
                                <th class="p-3">Base Unit</th>
                                <th class="p-3">Conversion Factor</th>
                                <th class="p-3">Equivalent</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($conversions as $conversion)

                            @php
                            $factor = rtrim(
                            rtrim($conversion->conversion_factor, '0'),
                            '.'
                            );
                            @endphp

                            <tr class="border-b">

                                <td class="p-3 font-medium">
                                    {{ $conversion->inventoryItem->item_name }}
                                </td>

                                <td class="p-3">
                                    {{ $conversion->fromUnit->unit_name }}
                                </td>

                                <td class="p-3">
                                    {{ $conversion->toUnit->unit_name }}
                                </td>

                                <td class="p-3">
                                    {{ $factor }}
                                </td>

                                <td class="p-3 text-gray-600">
                                    1 {{ $conversion->fromUnit->abbreviation }}
                                    =
                                    {{ $factor }}
                                    {{ $conversion->toUnit->abbreviation }}
                                </td>

                                <td class="p-3">

                                    <a href="{{ route('inventory.conversions.edit', $conversion) }}"
                                        class="text-blue-600 hover:underline">
                                        Edit
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="6"
                                    class="p-6 text-center text-gray-500">
                                    No unit conversions found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-6">
                    {{ $conversions->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>