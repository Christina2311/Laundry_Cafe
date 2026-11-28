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

    <a href="{{ route('inventory.conversions.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.conversions.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.conversions.*'),
        ])>
        Unit Conversions
    </a>

    <a href="{{ route('inventory.movements.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.movements.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.movements.*'),
        ])>
        Stock History
    </a>
    <a href="{{ route('inventory.low-stock.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.low-stock.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.low-stock.*'),
        ])>
        Low Stock Monitoring
    </a>
    <a href="{{ route('inventory.menu-items.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.menu-items.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.menu-items.*'),
        ])>
        Menu Items
    </a>
    <a href="{{ route('inventory.sellable-items.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.sellable-items.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.sellable-items.*'),
        ])>
        Café Products
    </a>
    <a href="{{ route('inventory.recipes.index') }}"
        @class([ 'text-sm' , 'font-semibold text-blue-600'=> request()->routeIs('inventory.recipes.*'),
        'text-gray-600 hover:underline' => !request()->routeIs('inventory.recipes.*'),
        ])>
        Recipes
    </a>
</div>