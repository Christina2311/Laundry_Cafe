<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Edit Measurement Unit
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <h3 class="mb-2 text-lg font-semibold">
                    Edit Measurement Unit
                </h3>

                <p class="mb-6 text-sm text-gray-500">
                    Update measurement unit information.
                </p>

                <form method="POST"
                    action="{{ route('inventory.units.update', $unit) }}">

                    @csrf
                    @method('PATCH')

                    @include('inventory.units._form')

                </form>

            </div>

        </div>
    </div>

</x-app-layout>