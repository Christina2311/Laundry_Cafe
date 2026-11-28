<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Café Product
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="mb-2 text-lg font-semibold">
                        New Sellable Product
                    </h3>

                    <p class="mb-6 text-sm text-gray-500">
                        Configure a café product and its selling price.
                    </p>

                    @if ($menuItems->isEmpty())

                    <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-700">

                        Create or activate a menu item first.

                        <a href="{{ route('inventory.menu-items.index') }}"
                            class="font-semibold underline">
                            Manage Menu Items
                        </a>

                    </div>

                    @else

                    <form method="POST"
                        action="{{ route('inventory.sellable-items.store') }}">

                        @csrf

                        @include('inventory.sellable-items._form', [
                        'buttonText' => 'Create Product',
                        ])

                    </form>

                    @endif

                </div>
            </div>

        </div>
    </div>

</x-app-layout>