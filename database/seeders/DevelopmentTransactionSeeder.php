<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\MenuItem;
use App\Models\SellableItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DevelopmentTransactionSeeder extends Seeder
{
    private const MARKER = 'DEMO-STOCK-HISTORY-V1';

    public function run(): void
    {
        // Prevent execution outside development.
        if (!app()->environment('local', 'testing')) {
            throw new RuntimeException(
                'This seeder is for development only.'
            );
        }

        DB::transaction(function () {

            // Prevent duplicate demo transactions.
            if (Purchase::where(
                'source_name',
                self::MARKER
            )->exists()) {
                $this->command->warn(
                    'Demo transactions already exist.'
                );

                return;
            }

            // Require a clean transaction database.
            if (
                Purchase::exists()
                || Order::exists()
                || StockMovement::exists()
                || SellableItem::exists()
            ) {
                throw new RuntimeException(
                    'Demo seeding requires an empty transaction database.'
                );
            }

            $service = app(InventoryService::class);

            $owner = User::whereHas(
                'role',
                fn($q) => $q->where('role_name', 'Owner')
            )->where('is_active', true)->firstOrFail();

            // Retrieve existing inventory items.

            $coffee = $this->getItem('Coffee Beans', 'g');

            $detergent = $this->getItem(
                'Detergent Powder',
                'g'
            );

            $laundryBags = $this->getItem(
                'Laundy Bags',
                'pcs'
            );

            $cups = $this->getItem(
                'Plastic Cups',
                'pcs'
            );

            // =====================================
            // 1. PURCHASE TRANSACTION
            // =====================================

            $purchase = Purchase::create([
                'supplier_id' => null,
                'recorded_by' => $owner->id,
                'source_type' => 'store',
                'source_name' => self::MARKER,
                'purchase_date' => now()->subDays(2),
                'purchase_status' => 'received',
                'total_amount' => '3300.00',
            ]);

            $purchaseItems = [
                [
                    'item' => $coffee,
                    'quantity' => '5000',
                    'unit_cost' => '0.40',
                    'line_total' => '2000.00',
                ],
                [
                    'item' => $detergent,
                    'quantity' => '10000',
                    'unit_cost' => '0.08',
                    'line_total' => '800.00',
                ],
                [
                    'item' => $laundryBags,
                    'quantity' => '100',
                    'unit_cost' => '2.00',
                    'line_total' => '200.00',
                ],
                [
                    'item' => $cups,
                    'quantity' => '100',
                    'unit_cost' => '3.00',
                    'line_total' => '300.00',
                ],
            ];

            foreach ($purchaseItems as $entry) {

                $item = $entry['item'];

                $detail = PurchaseDetail::create([
                    'purchase_id' => $purchase->purchase_id,
                    'inventory_item_id' => $item->inventory_item_id,
                    'quantity_purchased' => $entry['quantity'],
                    'purchase_unit_id' => $item->base_unit_id,
                    'conversion_factor' => '1.00000000',
                    'base_quantity' => $entry['quantity'],
                    'unit_cost' => $entry['unit_cost'],
                    'line_total' => $entry['line_total'],
                ]);

                $service->stockIn(
                    $item->inventory_item_id,
                    $entry['quantity'],
                    $owner->id,
                    'purchase',
                    [
                        'purchase_detail_id' =>
                        $detail->purchase_detail_id,
                    ],
                    self::MARKER . ' - Purchase'
                );
            }

            // =====================================
            // 2. TEMPORARY SELLABLE ITEMS
            // =====================================

            $menuItem = MenuItem::create([
                'menu_item_name' => 'DEMO - Americano',
                'category' => 'Coffee',
                'description' => self::MARKER,
                'is_active' => true,
            ]);

            $americano = SellableItem::create([
                'menu_item_id' => $menuItem->menu_item_id,
                'item_name' => 'DEMO - Americano',
                'item_type' => 'cafe',
                'current_price' => '100.00',
                'counts_as_load' => false,
                'counts_as_piece' => false,
                'is_active' => true,
            ]);

            $laundry = SellableItem::create([
                'menu_item_id' => null,
                'item_name' => 'DEMO - Regular Laundry',
                'item_type' => 'laundry',
                'current_price' => '150.00',
                'counts_as_load' => true,
                'counts_as_piece' => false,
                'is_active' => true,
            ]);

            // =====================================
            // 3. CAFE ORDER
            // =====================================

            $cafeDetail = $this->createPaidOrder(
                $owner,
                $americano,
                '5',
                '100.00',
                '500.00'
            );

            // 5 drinks x 18 grams of coffee.

            $service->stockOut(
                $coffee->inventory_item_id,
                '90',
                $owner->id,
                'sale',
                [
                    'order_detail_id' =>
                    $cafeDetail->order_detail_id,
                ],
                self::MARKER . ' - Coffee consumption'
            );

            // 5 drinks x 1 plastic cup.

            $service->stockOut(
                $cups->inventory_item_id,
                '5',
                $owner->id,
                'sale',
                [
                    'order_detail_id' =>
                    $cafeDetail->order_detail_id,
                ],
                self::MARKER . ' - Cup consumption'
            );

            // =====================================
            // 4. LAUNDRY ORDER
            // =====================================

            $laundryDetail = $this->createPaidOrder(
                $owner,
                $laundry,
                '3',
                '150.00',
                '450.00'
            );

            // 3 loads x 50 grams of detergent.

            $service->stockOut(
                $detergent->inventory_item_id,
                '150',
                $owner->id,
                'laundry_consumption',
                [
                    'order_detail_id' =>
                    $laundryDetail->order_detail_id,
                ],
                self::MARKER . ' - Detergent consumption'
            );

            // 3 loads x 1 laundry bag.

            $service->stockOut(
                $laundryBags->inventory_item_id,
                '3',
                $owner->id,
                'laundry_consumption',
                [
                    'order_detail_id' =>
                    $laundryDetail->order_detail_id,
                ],
                self::MARKER . ' - Laundry bag consumption'
            );

            // =====================================
            // 5. VERIFY RESULTS
            // =====================================

            $expectedBalances = [
                [$coffee, '4910.0000'],
                [$detergent, '9850.0000'],
                [$laundryBags, '97.0000'],
                [$cups, '95.0000'],
            ];

            foreach ($expectedBalances as [$item, $expected]) {
                if ($item->fresh()->current_stock !== $expected) {
                    throw new RuntimeException(
                        "Unexpected balance for {$item->item_name}."
                    );
                }
            }

            if (StockMovement::count() !== 8) {
                throw new RuntimeException(
                    'Unexpected stock movement count.'
                );
            }

            $this->command->info(
                'Development transactions created successfully.'
            );

            $this->command->info('Purchases: 1');
            $this->command->info('Orders: 2');
            $this->command->info('Payments: 2');
            $this->command->info('Stock Movements: 8');
        });
    }

    private function getItem(
        string $name,
        string $expectedUnit
    ): InventoryItem {
        $item = InventoryItem::with('baseUnit')
            ->where('item_name', $name)
            ->where('is_active', true)
            ->firstOrFail();

        if ($item->baseUnit->abbreviation !== $expectedUnit) {
            throw new RuntimeException(
                "Incorrect base unit for {$name}."
            );
        }

        if ($item->current_stock !== '0.0000') {
            throw new RuntimeException(
                "{$name} must have zero initial stock."
            );
        }

        return $item;
    }

    private function createPaidOrder(
        User $owner,
        SellableItem $sellable,
        string $quantity,
        string $unitPrice,
        string $total
    ): OrderDetail {
        $order = Order::create([
            'processed_by' => $owner->id,
            'discount_id' => null,
            'order_datetime' => now(),
            'order_status' => 'completed',
            'subtotal' => $total,
            'discount_amount' => '0.00',
            'total_amount' => $total,
            'notes' => self::MARKER,
        ]);

        $detail = OrderDetail::create([
            'order_id' => $order->order_id,
            'item_id' => $sellable->item_id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $total,
        ]);

        Payment::create([
            'order_id' => $order->order_id,
            'payment_method' => 'cash',
            'amount_paid' => $total,
            'payment_status' => 'verified',
            'receipt_image_path' => null,
            'verified_by' => $owner->id,
            'payment_date' => now(),
            'verification_date' => now(),
        ]);

        return $detail;
    }
}
