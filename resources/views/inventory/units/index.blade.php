<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Measurement Units
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
                            Measurement Units
                        </h3>

                        <p class="text-sm text-gray-500">
                            Manage units for cafe and laundry inventory.
                        </p>
                    </div>

                    <a href="{{ route('inventory.units.create') }}"
                        class="rounded bg-gray-800 px-4 py-2 text-white">
                        + Add Unit
                    </a>

                </div>

                {{-- Inventory Navigation --}}
                <div class="mb-6 flex gap-4 border-b pb-3">

                    @include('inventory._navigation')

                </div>

                {{-- Search --}}
                <form method="GET"
                    action="{{ route('inventory.units.index') }}"
                    class="mb-6 flex flex-wrap items-center gap-3">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search units..."
                        class="rounded-md border-gray-300">

                    <x-primary-button>
                        Search
                    </x-primary-button>

                    <a href="{{ route('inventory.units.index') }}"
                        class="text-sm text-gray-600 hover:underline">
                        Reset
                    </a>

                </form>

                {{-- Units Table --}}
                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Unit Name</th>
                                <th class="p-3">Abbreviation</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($units as $unit)

                            <tr class="border-b">

                                <td class="p-3 font-medium">
                                    {{ $unit->unit_name }}
                                </td>

                                <td class="p-3">
                                    {{ $unit->abbreviation }}
                                </td>

                                <td class="p-3">

                                    <a href="{{ route('inventory.units.edit', $unit) }}"
                                        class="text-blue-600 hover:underline">
                                        Edit
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="3"
                                    class="p-6 text-center text-gray-500">
                                    No measurement units found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-6">
                    {{ $units->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>