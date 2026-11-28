<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Menu Item Management
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
                                Café Menu Items
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Manage café products and their general information.
                            </p>
                        </div>

                        <a href="{{ route('inventory.menu-items.create') }}"
                            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            + Add Menu Item
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
                        action="{{ route('inventory.menu-items.index') }}"
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
                                    placeholder="Search menu items..."
                                    :value="request('search')" />
                            </div>

                            <div>
                                <x-input-label
                                    for="category"
                                    value="Category" />

                                <select
                                    id="category"
                                    name="category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Categories
                                    </option>

                                    @foreach ($categories as $category)
                                    <option value="{{ $category }}"
                                        @selected(request('category')===$category)>
                                        {{ $category }}
                                    </option>
                                    @endforeach

                                </select>
                            </div>

                            <div>
                                <x-input-label
                                    for="status"
                                    value="Status" />

                                <select
                                    id="status"
                                    name="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">All Statuses</option>

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

                            <a href="{{ route('inventory.menu-items.index') }}"
                                class="text-sm text-gray-600 hover:underline">
                                Reset Filters
                            </a>

                        </div>

                    </form>

                    <p class="mb-3 text-sm text-gray-500">
                        {{ $menuItems->total() }} menu item(s) found.
                    </p>

                    {{-- Menu Items Table --}}
                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200 text-sm">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        Menu Item
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Category
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Description
                                    </th>

                                    <th class="px-4 py-3 text-center">
                                        Sellable Products
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

                                @forelse ($menuItems as $item)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3 font-medium">
                                        {{ $item->menu_item_name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $item->category }}
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        {{ \Illuminate\Support\Str::limit($item->description ?? '—', 80) }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $item->sellable_items_count }}
                                    </td>

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

                                    <td class="px-4 py-3">

                                        <div class="flex items-center gap-3">

                                            <a href="{{ route('inventory.menu-items.edit', $item) }}"
                                                class="font-medium text-blue-600 hover:underline">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                action="{{ route('inventory.menu-items.update-status', $item) }}"
                                                onsubmit="return confirm('Are you sure you want to change this menu item status?')">

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
                                        No menu items found.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-6">
                        {{ $menuItems->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>