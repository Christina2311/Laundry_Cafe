<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Create Unit Conversion
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <h3 class="mb-2 text-lg font-semibold">
                    New Unit Conversion
                </h3>

                <p class="mb-6 text-sm text-gray-500">
                    Define how a measurement unit converts
                    into an inventory item's base unit.
                </p>

                <form method="POST"
                    action="{{ route('inventory.conversions.store') }}">

                    @csrf

                    @include('inventory.conversions._form')

                </form>

            </div>

        </div>
    </div>

</x-app-layout>