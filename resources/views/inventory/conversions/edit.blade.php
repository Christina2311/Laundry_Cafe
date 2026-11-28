<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Edit Unit Conversion
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <h3 class="mb-2 text-lg font-semibold">
                    Edit Unit Conversion
                </h3>

                <p class="mb-6 text-sm text-gray-500">
                    Update the conversion factor.
                    Conversions already in use cannot be modified.
                </p>

                <form method="POST"
                    action="{{ route('inventory.conversions.update', $conversion) }}">

                    @csrf
                    @method('PATCH')

                    @include('inventory.conversions._form')

                </form>

            </div>

        </div>
    </div>

</x-app-layout>