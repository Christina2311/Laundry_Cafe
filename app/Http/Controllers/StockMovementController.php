<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $reasons = [
            'purchase' => 'Purchase',
            'sale' => 'Café Sale',
            'laundry_consumption' => 'Laundry Consumption',
            'cancellation' => 'Cancellation',
            'refund' => 'Refund',
            'purchase_reversal' => 'Purchase Reversal',
        ];

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'category_id' => [
                'nullable',
                'integer',
                'exists:inventory_categories,category_id',
            ],
            'movement_type' => [
                'nullable',
                Rule::in(['in', 'out']),
            ],
            'movement_reason' => [
                'nullable',
                Rule::in(array_keys($reasons)),
            ],
            'date_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
        ]);

        $query = StockMovement::with([
            'inventoryItem.category',
            'inventoryItem.baseUnit',
            'recordedBy',
            'purchaseDetail',
            'orderDetail',
        ]);

        // Search by inventory item name.
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->whereHas(
                'inventoryItem',
                function ($q) use ($search) {
                    $q->where(
                        'item_name',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        // Filter by inventory category.
        if (!empty($filters['category_id'])) {
            $query->whereHas(
                'inventoryItem',
                function ($q) use ($filters) {
                    $q->where(
                        'category_id',
                        $filters['category_id']
                    );
                }
            );
        }

        // Filter by stock movement type.
        if (!empty($filters['movement_type'])) {
            $query->where(
                'movement_type',
                $filters['movement_type']
            );
        }

        // Filter by movement reason.
        if (!empty($filters['movement_reason'])) {
            $query->where(
                'movement_reason',
                $filters['movement_reason']
            );
        }

        // Filter by starting date.
        if (!empty($filters['date_from'])) {
            $query->whereDate(
                'movement_date',
                '>=',
                $filters['date_from']
            );
        }

        // Filter by ending date.
        if (!empty($filters['date_to'])) {
            $query->whereDate(
                'movement_date',
                '<=',
                $filters['date_to']
            );
        }

        $movements = $query
            ->orderByDesc('movement_date')
            ->orderByDesc('movement_id')
            ->paginate(20)
            ->withQueryString();

        $categories = InventoryCategory::orderBy(
            'category_name'
        )->get();

        return view('inventory.movements.index', [
            'movements' => $movements,
            'categories' => $categories,
            'reasons' => $reasons,
        ]);
    }
}
