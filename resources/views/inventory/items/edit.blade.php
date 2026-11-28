<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Edit Inventory Item
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <h3 class="mb-2 text-lg font-semibold">
                    Edit Inventory Item
                </h3>

                <p class="mb-6 text-sm text-gray-500">
                    Update inventory item information and reorder level.
                </p>

                <form method="POST"
                    action="{{ route('inventory.items.update', $item) }}">

                    @csrf
                    @method('PATCH')

                    @include('inventory.items._form')

                </form>

            </div>

        </div>
    </div>

</x-app-layout>