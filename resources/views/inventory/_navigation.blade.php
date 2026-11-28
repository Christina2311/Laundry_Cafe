<div class="mb-6 flex flex-wrap gap-5 border-b pb-3">

    <a href="{{ route('inventory.items.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.items.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.items.*'),
        ])>
        Inventory Items
    </a>

    <a href="{{ route('inventory.categories.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.categories.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.categories.*'),
        ])>
        Categories
    </a>

    <a href="{{ route('inventory.units.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.units.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.units.*'),
        ])>
        Measurement Units
    </a>

</div>