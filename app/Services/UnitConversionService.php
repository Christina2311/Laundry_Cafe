<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\UnitConversion;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Math\Exception\MathException;
use Illuminate\Validation\ValidationException;

class UnitConversionService
{
    /**
     * Maximum quantity supported by DECIMAL(15,4).
     */
    private const MAX_QUANTITY = '99999999999.9999';

    /**
     * Convert a quantity into the inventory item's base unit.
     */
    public function convertToBase(
        InventoryItem $item,
        int $fromUnitId,
        string|int $quantity
    ): array {
        // Reject inactive inventory items.
        if (!$item->is_active) {
            throw ValidationException::withMessages([
                'inventory_item_id' =>
                'The inventory item is inactive.',
            ]);
        }

        // Validate the input quantity.
        $inputQuantity = $this->validateQuantity($quantity);

        // Get the item's base unit.
        $baseUnitId = (int) $item->base_unit_id;

        // No conversion is required if units are identical.
        if ($fromUnitId === $baseUnitId) {
            $factor = BigDecimal::of('1');
        } else {
            $conversion = UnitConversion::where(
                'inventory_item_id',
                $item->inventory_item_id
            )
                ->where('from_unit_id', $fromUnitId)
                ->where('to_unit_id', $baseUnitId)
                ->first();

            // Reject missing conversions.
            if (!$conversion) {
                throw ValidationException::withMessages([
                    'unit_id' =>
                    "No unit conversion exists for {$item->item_name}.",
                ]);
            }

            $factor = BigDecimal::of(
                $conversion->conversion_factor
            );
        }

        // Protect against invalid stored conversion factors.
        if ($factor->isLessThanOrEqualTo('0')) {
            throw ValidationException::withMessages([
                'conversion_factor' =>
                'The conversion factor must be greater than zero.',
            ]);
        }

        // Calculate the converted quantity.
        $baseQuantity = $inputQuantity->multipliedBy($factor);

        // Validate the result against database limits.
        $baseQuantity = $this->validateQuantity(
            (string) $baseQuantity
        );

        return [
            'inventory_item_id' => $item->inventory_item_id,
            'from_unit_id' => $fromUnitId,
            'base_unit_id' => $baseUnitId,
            'original_quantity' => (string) $inputQuantity,
            'conversion_factor' => (string) $factor->toScale(
                8,
                RoundingMode::Unnecessary
            ),
            'base_quantity' => (string) $baseQuantity,
        ];
    }

    /**
     * Validate positive quantities and database precision.
     */
    private function validateQuantity(
        string|int $quantity
    ): BigDecimal {
        try {
            $decimal = BigDecimal::of($quantity);

            if ($decimal->isLessThanOrEqualTo('0')) {
                throw ValidationException::withMessages([
                    'quantity' =>
                    'Quantity must be greater than zero.',
                ]);
            }

            if ($decimal->isGreaterThan(self::MAX_QUANTITY)) {
                throw ValidationException::withMessages([
                    'quantity' =>
                    'Quantity exceeds the supported database limit.',
                ]);
            }

            // Reject values that require rounding.
            return $decimal->toScale(
                4,
                RoundingMode::Unnecessary
            );
        } catch (MathException $exception) {
            throw ValidationException::withMessages([
                'quantity' =>
                'Quantity must be a valid number representable with 4 decimal places.',
            ]);
        }
    }
}
