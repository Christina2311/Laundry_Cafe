<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Inventory Categories
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

                <div class="mb-6 flex items-center justify-between">

                    <div>
                        <h3 class="text-lg font-semibold">
                            Inventory Categories
                        </h3>

                        <p class="text-sm text-gray-500">
                            Manage cafe and laundry supply categories.
                        </p>
                    </div>

                    <a href="{{ route('inventory.categories.create') }}"
                        class="rounded bg-gray-800 px-4 py-2 text-white">
                        + Add Category
                    </a>

                </div>
                <div class="mb-6 flex gap-4 border-b pb-3">

                    @include('inventory._navigation')

                </div>
                {{-- Search --}}
                <form method="GET"
                    action="{{ route('inventory.categories.index') }}"
                    class="mb-6 flex flex-wrap items-center gap-3">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search categories..."
                        class="rounded-md border-gray-300">

                    <x-primary-button>
                        Search
                    </x-primary-button>

                    <a href="{{ route('inventory.categories.index') }}"
                        class="text-sm text-gray-600 hover:underline">
                        Reset
                    </a>

                </form>

                {{-- Category Table --}}
                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Category Name</th>
                                <th class="p-3">Description</th>
                                <th class="p-3">Total Items</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($categories as $category)

                            <tr class="border-b">

                                <td class="p-3 font-medium">
                                    {{ $category->category_name }}
                                </td>

                                <td class="p-3 text-gray-600">
                                    {{ $category->description ?? '—' }}
                                </td>

                                <td class="p-3">
                                    {{ $category->inventory_items_count }}
                                </td>

                                <td class="p-3">

                                    <a href="{{ route('inventory.categories.edit', $category) }}"
                                        class="text-blue-600 hover:underline">
                                        Edit
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="4"
                                    class="p-6 text-center text-gray-500">
                                    No inventory categories found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-6">
                    {{ $categories->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>