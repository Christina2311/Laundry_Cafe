<?php

use App\Models\User;
use App\Models\InventoryItem;
use App\Models\MeasurementUnit;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\SellableItem;
use App\Models\StockMovement;
use App\Services\InventoryService;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Brick\Math\BigDecimal;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(
    Illuminate\Contracts\Console\Kernel::class
)->bootstrap();

if (!app()->environment('local', 'testing')) {
    exit("Run this script in a development environment only.\n");
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function expectRejection(callable $callback, string $message): void
{
    try {
        $callback();
    } catch (ValidationException $e) {
        if (!str_contains($e->getMessage(), $message)) {
            throw $e;
        }

        return;
    }

    throw new RuntimeException('Expected validation error was not thrown.');
}

$passed = 0;

DB::beginTransaction();

try {
    $service = app(InventoryService::class);
    $converter = app(UnitConversionService::class);

    $owner = User::whereHas(
        'role',
        fn($q) => $q->where('role_name', 'Owner')
    )->where('is_active', true)->firstOrFail();

    $item = InventoryItem::where(
        'item_name',
        'Detergent Powder'
    )->firstOrFail();

    $bag = MeasurementUnit::where(
        'abbreviation',
        'bag'
    )->firstOrFail();

    $initialStock = $item->current_stock;
    $initialCount = StockMovement::count();

    // Prepare temporary purchase.

    $converted = $converter->convertToBase(
        $item,
        $bag->unit_id,
        '2'
    );

    $purchase = Purchase::create([
        'supplier_id' => null,
        'recorded_by' => $owner->id,
        'source_type' => 'store',
        'source_name' => 'Temporary Test Store',
        'purchase_date' => now(),
        'purchase_status' => 'received',
        'total_amount' => '1000.00',
    ]);

    $detail = PurchaseDetail::create([
        'purchase_id' => $purchase->purchase_id,
        'inventory_item_id' => $item->inventory_item_id,
        'quantity_purchased' => '2.0000',
        'purchase_unit_id' => $bag->unit_id,
        'conversion_factor' => $converted['conversion_factor'],
        'base_quantity' => $converted['base_quantity'],
        'unit_cost' => '500.00',
        'line_total' => '1000.00',
    ]);

    $service->stockIn(
        $item->inventory_item_id,
        $converted['base_quantity'],
        $owner->id,
        'purchase',
        ['purchase_detail_id' => $detail->purchase_detail_id]
    );

    // Prepare temporary laundry service.

    $sellable = SellableItem::create([
        'menu_item_id' => null,
        'item_name' => 'Temporary Laundry Service',
        'item_type' => 'laundry',
        'current_price' => '150.00',
        'counts_as_load' => true,
        'counts_as_piece' => false,
        'is_active' => true,
    ]);

    $order = Order::create([
        'processed_by' => $owner->id,
        'discount_id' => null,
        'order_datetime' => now(),
        'order_status' => 'completed',
        'subtotal' => '450.00',
        'discount_amount' => '0.00',
        'total_amount' => '450.00',
        'notes' => 'Temporary inventory test',
    ]);

    $orderDetail = OrderDetail::create([
        'order_id' => $order->order_id,
        'item_id' => $sellable->item_id,
        'quantity' => '3.0000',
        'unit_price' => '150.00',
        'line_total' => '450.00',
    ]);

    Payment::create([
        'order_id' => $order->order_id,
        'payment_method' => 'cash',
        'amount_paid' => '450.00',
        'payment_status' => 'verified',
        'receipt_image_path' => null,
        'verified_by' => $owner->id,
        'payment_date' => now(),
        'verification_date' => now(),
    ]);

    $source = [
        'order_detail_id' => $orderDetail->order_detail_id,
    ];

    $stockBefore = $item->fresh()->current_stock;
    $countBefore = StockMovement::count();

    // TEST 13: Invalid transaction source.

    expectRejection(
        fn() => $service->stockOut(
            $item->inventory_item_id,
            '100',
            $owner->id,
            'laundry_consumption',
            []
        ),
        'A valid order_detail_id is required'
    );

    check(
        $item->fresh()->current_stock === $stockBefore,
        'Stock changed after invalid source.'
    );

    check(
        StockMovement::count() === $countBefore,
        'Unexpected stock movement created.'
    );

    echo "[PASS] Test 13 - Invalid transaction source\n";
    $passed++;

    // TEST 14: Inactive inventory protection.

    $item->fresh()->update(['is_active' => false]);

    check(
        !$service->hasSufficientStock(
            $item->inventory_item_id,
            '100'
        ),
        'Inactive item reported sufficient stock.'
    );

    expectRejection(
        fn() => $service->stockOut(
            $item->inventory_item_id,
            '100',
            $owner->id,
            'laundry_consumption',
            $source
        ),
        'The inventory item is inactive'
    );

    check(
        $item->fresh()->current_stock === $stockBefore,
        'Inactive item stock was modified.'
    );

    check(
        StockMovement::count() === $countBefore,
        'Unexpected movement for inactive item.'
    );

    $item->fresh()->update(['is_active' => true]);

    echo "[PASS] Test 14 - Inactive inventory protection\n";
    $passed++;

    // TEST 15: Nested transaction rollback.

    DB::beginTransaction();

    try {
        $service->stockOut(
            $item->inventory_item_id,
            '100',
            $owner->id,
            'laundry_consumption',
            $source
        );

        check(
            BigDecimal::of($stockBefore)
                ->minus('100')
                ->isEqualTo($item->fresh()->current_stock),
            'Incorrect stock deduction.'
        );

        check(
            StockMovement::count() === $countBefore + 1,
            'Stock movement was not created.'
        );
    } finally {
        DB::rollBack();
    }

    check(
        $item->fresh()->current_stock === $stockBefore,
        'Stock was not restored after rollback.'
    );

    check(
        StockMovement::count() === $countBefore,
        'Movement was not removed after rollback.'
    );

    echo "[PASS] Test 15 - Transaction rollback\n";
    $passed++;

    // TEST 16: Stock balance reconciliation.

    $service->stockOut(
        $item->inventory_item_id,
        '150',
        $owner->id,
        'laundry_consumption',
        $source
    );

    $movementBalance = StockMovement::where(
        'inventory_item_id',
        $item->inventory_item_id
    )->sum('quantity_change');

    check(
        BigDecimal::of($movementBalance)
            ->isEqualTo($item->fresh()->current_stock),
        'Inventory balance does not match stock movements.'
    );

    echo "[PASS] Test 16 - Stock balance reconciliation\n";
    $passed++;

    echo "\nFinal test stock: ";
    echo $item->fresh()->current_stock . " g\n";
} catch (Throwable $e) {
    echo "\n[FAIL] " . $e->getMessage() . "\n";
} finally {
    while (DB::transactionLevel() > 0) {
        DB::rollBack();
    }

    echo "\nTemporary transactions rolled back.\n";
}

echo "\nResults: {$passed}/4 tests passed.\n";

if (isset($item, $initialStock, $initialCount)) {
    $clean = $item->fresh()->current_stock === $initialStock
        && StockMovement::count() === $initialCount
        && DB::transactionLevel() === 0
        && (!isset($purchase) || Purchase::find($purchase->purchase_id) === null)
        && (!isset($order) || Order::find($order->order_id) === null)
        && (!isset($sellable) || SellableItem::find($sellable->item_id) === null);

    echo $clean
        ? "[PASS] Database cleanup verified.\n"
        : "[FAIL] Database cleanup verification failed.\n";
} else {
    $clean = false;
}

exit($passed === 4 && $clean ? 0 : 1);
