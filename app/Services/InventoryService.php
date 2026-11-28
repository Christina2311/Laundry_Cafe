<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\PurchaseDetail;
use App\Models\OrderDetail;
use App\Models\User;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Math\Exception\MathException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    private const MAX_QUANTITY = '99999999999.9999';

    /**
     * Increase inventory stock.
     */
    public function stockIn(
        int $inventoryItemId,
        string|int $quantity,
        int $recordedBy,
        string $reason,
        array $source,
        ?string $remarks = null
    ): StockMovement {
        return $this->recordMovement(
            $inventoryItemId,
            $quantity,
            $recordedBy,
            'in',
            $reason,
            $source,
            $remarks
        );
    }

    /**
     * Decrease inventory stock.
     */
    public function stockOut(
        int $inventoryItemId,
        string|int $quantity,
        int $recordedBy,
        string $reason,
        array $source,
        ?string $remarks = null
    ): StockMovement {
        return $this->recordMovement(
            $inventoryItemId,
            $quantity,
            $recordedBy,
            'out',
            $reason,
            $source,
            $remarks
        );
    }

    /**
     * Check whether an inventory item has sufficient stock.
     *
     * This is a preliminary check only.
     * Stock must be checked again during deduction.
     */
    public function hasSufficientStock(
        int $inventoryItemId,
        string|int $quantity
    ): bool {
        $item = InventoryItem::findOrFail($inventoryItemId);

        $required = $this->validateQuantity($quantity);

        if (!$item->is_active) {
            return false;
        }

        return BigDecimal::of($item->current_stock)
            ->isGreaterThanOrEqualTo($required);
    }

    /**
     * Record an inventory movement and update stock.
     */
    private function recordMovement(
        int $inventoryItemId,
        string|int $quantity,
        int $recordedBy,
        string $type,
        string $reason,
        array $source,
        ?string $remarks
    ): StockMovement {
        $amount = $this->validateQuantity($quantity);

        return DB::transaction(function () use (
            $inventoryItemId,
            $amount,
            $recordedBy,
            $type,
            $reason,
            $source,
            $remarks
        ) {
            // Lock inventory to prevent simultaneous stock changes.
            $item = InventoryItem::lockForUpdate()
                ->findOrFail($inventoryItemId);

            if (!$item->is_active) {
                throw ValidationException::withMessages([
                    'inventory_item_id' =>
                    'The inventory item is inactive.',
                ]);
            }

            // Validate the user responsible for the movement.
            $user = User::findOrFail($recordedBy);

            if (!$user->isActive()) {
                throw ValidationException::withMessages([
                    'recorded_by' =>
                    'The user account is inactive.',
                ]);
            }

            // Validate the movement reason and transaction source.
            $source = $this->validateSource(
                $item,
                $type,
                $reason,
                $source
            );

            // Ensure stock-in matches the recorded purchase quantity.
            if ($reason === 'purchase') {
                $purchaseDetail = PurchaseDetail::findOrFail(
                    $source['purchase_detail_id']
                );

                $expectedQuantity = BigDecimal::of(
                    $purchaseDetail->base_quantity
                );

                if (!$amount->isEqualTo($expectedQuantity)) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                        'Stock-in quantity must match the recorded purchase quantity.',
                    ]);
                }
            }

            // Prevent duplicate inventory movements.
            $duplicate = StockMovement::where(
                'inventory_item_id',
                $inventoryItemId
            )
                ->where('movement_reason', $reason)
                ->where('movement_type', $type)
                ->where(
                    'purchase_detail_id',
                    $source['purchase_detail_id']
                )
                ->where(
                    'order_detail_id',
                    $source['order_detail_id']
                )
                ->whereNull('laundry_order_id')
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'stock_movement' =>
                    'This inventory movement has already been recorded.',
                ]);
            }

            $currentStock = BigDecimal::of(
                $item->current_stock
            );

            // Calculate the new inventory balance.
            if ($type === 'in') {
                $newStock = $currentStock->plus($amount);

                $quantityChange = $amount;
            } else {
                if ($currentStock->isLessThan($amount)) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                        "Insufficient stock for {$item->item_name}. "
                            . "Available: {$item->current_stock}. "
                            . "Required: {$amount}.",
                    ]);
                }

                $newStock = $currentStock->minus($amount);

                $quantityChange = $amount->negated();
            }

            // Protect against database overflow.
            if ($newStock->isGreaterThan(self::MAX_QUANTITY)) {
                throw ValidationException::withMessages([
                    'quantity' =>
                    'The resulting stock exceeds the database limit.',
                ]);
            }

            // Update the inventory balance.
            $item->current_stock = (string) $newStock->toScale(
                4,
                RoundingMode::Unnecessary
            );

            $item->save();

            // Record the stock movement.
            return StockMovement::create([
                'inventory_item_id' => $inventoryItemId,

                'purchase_detail_id' =>
                $source['purchase_detail_id'],

                'order_detail_id' =>
                $source['order_detail_id'],

                'laundry_order_id' => null,

                'recorded_by' => $recordedBy,

                'movement_type' => $type,

                'movement_reason' => $reason,

                'quantity_change' =>
                (string) $quantityChange->toScale(
                    4,
                    RoundingMode::Unnecessary
                ),

                'movement_date' => now(),

                'remarks' => $remarks,
            ]);
        });
    }

    /**
     * Validate the quantity.
     */
    private function validateQuantity(
        string|int $quantity
    ): BigDecimal {
        try {
            $amount = BigDecimal::of($quantity);

            if ($amount->isLessThanOrEqualTo('0')) {
                throw ValidationException::withMessages([
                    'quantity' =>
                    'Quantity must be greater than zero.',
                ]);
            }

            if ($amount->isGreaterThan(self::MAX_QUANTITY)) {
                throw ValidationException::withMessages([
                    'quantity' =>
                    'Quantity exceeds the database limit.',
                ]);
            }

            return $amount->toScale(
                4,
                RoundingMode::Unnecessary
            );
        } catch (MathException $exception) {
            throw ValidationException::withMessages([
                'quantity' =>
                'Quantity must be representable with 4 decimal places.',
            ]);
        }
    }

    /**
     * Validate the stock movement source.
     */
    private function validateSource(
        InventoryItem $item,
        string $type,
        string $reason,
        array $source
    ): array {
        // Initial MVP supports purchases and order deductions.
        $allowed = [
            'purchase' => [
                'type' => 'in',
                'source' => 'purchase_detail_id',
            ],

            'sale' => [
                'type' => 'out',
                'source' => 'order_detail_id',
            ],

            'laundry_consumption' => [
                'type' => 'out',
                'source' => 'order_detail_id',
            ],
        ];

        if (
            !isset($allowed[$reason]) ||
            $allowed[$reason]['type'] !== $type
        ) {
            throw ValidationException::withMessages([
                'movement_reason' =>
                'Invalid or unsupported stock movement reason.',
            ]);
        }

        $sourceKey = $allowed[$reason]['source'];

        // Exactly one transaction source is required.
        if (
            count($source) !== 1 ||
            !isset($source[$sourceKey])
        ) {
            throw ValidationException::withMessages([
                'source' =>
                "A valid {$sourceKey} is required.",
            ]);
        }

        $sourceId = filter_var(
            $source[$sourceKey],
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($sourceId === false) {
            throw ValidationException::withMessages([
                'source' =>
                'Invalid transaction source ID.',
            ]);
        }

        if ($sourceKey === 'purchase_detail_id') {
            $detail = PurchaseDetail::findOrFail($sourceId);

            if (
                (int) $detail->inventory_item_id !==
                (int) $item->inventory_item_id
            ) {
                throw ValidationException::withMessages([
                    'source' =>
                    'The purchase detail belongs to another inventory item.',
                ]);
            }

            $purchase = DB::table('purchases')
                ->where('purchase_id', $detail->purchase_id)
                ->first();

            if (!$purchase || $purchase->purchase_status !== 'received') {
                throw ValidationException::withMessages([
                    'source' =>
                    'Only received purchases can increase stock.',
                ]);
            }
        }

        if ($sourceKey === 'order_detail_id') {
            $detail = OrderDetail::findOrFail($sourceId);

            $sellable = DB::table('sellable_items')
                ->where('item_id', $detail->item_id)
                ->first();

            $expectedType = $reason === 'sale'
                ? 'cafe'
                : 'laundry';

            if (!$sellable || $sellable->item_type !== $expectedType) {
                throw ValidationException::withMessages([
                    'source' =>
                    'The order item does not match the movement reason.',
                ]);
            }

            $order = DB::table('orders')
                ->where('order_id', $detail->order_id)
                ->first();

            $verified = DB::table('payments')
                ->where('order_id', $detail->order_id)
                ->where('payment_status', 'verified')
                ->exists();

            if (!$order || $order->order_status !== 'completed' || !$verified) {
                throw ValidationException::withMessages([
                    'source' =>
                    'Stock deductions require a completed and paid order.',
                ]);
            }
        }

        return [
            'purchase_detail_id' =>
            $sourceKey === 'purchase_detail_id'
                ? $sourceId
                : null,

            'order_detail_id' =>
            $sourceKey === 'order_detail_id'
                ? $sourceId
                : null,
        ];
    }
}
