<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LowStockController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'category_id' => [
                'nullable',
                'integer',
                'exists:inventory_categories,category_id',
            ],
            'stock_status' => [
                'nullable',
                Rule::in(['out', 'low', 'sufficient']),
            ],
        ]);

        $baseQuery = InventoryItem::where('is_active', true);

        // Overall stock summary.
        $summary = [
            'total' => (clone $baseQuery)->count(),

            'out' => (clone $baseQuery)
                ->where('current_stock', 0)
                ->count(),

            'low' => (clone $baseQuery)
                ->where('current_stock', '>', 0)
                ->whereColumn('current_stock', '<=', 'reorder_level')
                ->count(),

            'sufficient' => (clone $baseQuery)
                ->whereColumn('current_stock', '>', 'reorder_level')
                ->count(),
        ];

        $query = (clone $baseQuery)->with([
            'category',
            'baseUnit',
        ]);

        // Search by inventory item.
        if (!empty($filters['search'])) {
            $query->where(
                'item_name',
                'like',
                '%' . $filters['search'] . '%'
            );
        }

        // Filter by category.
        if (!empty($filters['category_id'])) {
            $query->where(
                'category_id',
                $filters['category_id']
            );
        }

        // Filter by stock status.
        switch ($filters['stock_status'] ?? null) {
            case 'out':
                $query->where('current_stock', 0);
                break;

            case 'low':
                $query->where('current_stock', '>', 0)
                    ->whereColumn(
                        'current_stock',
                        '<=',
                        'reorder_level'
                    );
                break;

            case 'sufficient':
                $query->whereColumn(
                    'current_stock',
                    '>',
                    'reorder_level'
                );
                break;
        }

        // Prioritize items requiring replenishment.
        $items = $query->orderByRaw("
            CASE
                WHEN current_stock = 0 THEN 1
                WHEN current_stock <= reorder_level THEN 2
                ELSE 3
            END
        ")
            ->orderBy('item_name')
            ->paginate(15)
            ->withQueryString();

        $categories = InventoryCategory::orderBy(
            'category_name'
        )->get();

        return view('inventory.low-stock.index', [
            'items' => $items,
            'categories' => $categories,
            'summary' => $summary,
        ]);
    }
}
