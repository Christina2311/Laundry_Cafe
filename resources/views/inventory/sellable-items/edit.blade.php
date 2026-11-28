<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Café Product
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="mb-2 text-lg font-semibold">
                        Edit {{ $item->item_name }}
                    </h3>

                    <p class="mb-6 text-sm text-gray-500">
                        Update the product name and selling price.
                    </p>

                    <form method="POST"
                        action="{{ route('inventory.sellable-items.update', $item) }}">

                        @csrf
                        @method('PUT')

                        @include('inventory.sellable-items._form', [
                        'buttonText' => 'Update Product',
                        ])

                    </form>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>