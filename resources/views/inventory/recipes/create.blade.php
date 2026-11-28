<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Recipe
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="mb-2 text-lg font-semibold">
                        New Product Recipe
                    </h3>

                    <p class="mb-6 text-sm text-gray-500">
                        Create a recipe configuration for a café product.
                    </p>

                    @if ($products->isEmpty())

                    <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-700">

                        Create a café product first.

                        <a href="{{ route('inventory.sellable-items.index') }}"
                            class="font-semibold underline">
                            Manage Café Products
                        </a>

                    </div>

                    @else

                    <form method="POST"
                        action="{{ route('inventory.recipes.store') }}">

                        @csrf

                        @include('inventory.recipes._form', [
                        'buttonText' => 'Create Recipe',
                        ])

                    </form>

                    @endif

                </div>
            </div>

        </div>
    </div>

</x-app-layout>