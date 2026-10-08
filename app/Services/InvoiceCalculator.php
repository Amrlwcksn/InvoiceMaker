<?php

namespace App\Services;

class InvoiceCalculator
{
    /**
     * Helper to parse string or float number cleanly (supports both dot and comma decimal points).
     */
    private static function parseNumber(mixed $value, float $default = 0): float
    {
        if (is_null($value) || $value === '') {
            return $default;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        // Replace comma with dot for Indonesian locale numeric inputs
        $clean = str_replace(',', '.', (string) $value);
        return is_numeric($clean) ? (float) $clean : $default;
    }

    /**
     * Calculate all totals for an invoice and its items safely on backend.
     *
     * @param array $items Array of items with keys: item_name, description, quantity, unit_price, discount
     * @param float|string $invoiceDiscount Overall invoice discount
     * @param float|string $tax Overall tax amount
     * @return array Calculated results
     */
    public static function calculate(array $items, mixed $invoiceDiscount = 0, mixed $tax = 0): array
    {
        $calculatedItems = [];
        $subtotal = 0;

        foreach ($items as $item) {
            $qty = max(0, static::parseNumber($item['quantity'] ?? 1, 1));
            $unitPrice = max(0, static::parseNumber($item['unit_price'] ?? 0, 0));
            $itemDiscount = max(0, static::parseNumber($item['discount'] ?? 0, 0));

            $itemTotal = max(0, ($qty * $unitPrice) - $itemDiscount);

            $calculatedItems[] = [
                'item_name' => trim((string) ($item['item_name'] ?? 'Item')),
                'description' => !empty($item['description']) ? trim((string) $item['description']) : null,
                'quantity' => round($qty, 2),
                'unit_price' => round($unitPrice, 2),
                'discount' => round($itemDiscount, 2),
                'total' => round($itemTotal, 2),
            ];

            $subtotal += $itemTotal;
        }

        $invDiscountNum = max(0, static::parseNumber($invoiceDiscount, 0));
        $taxNum = max(0, static::parseNumber($tax, 0));

        $total = max(0, ($subtotal - $invDiscountNum) + $taxNum);

        return [
            'items' => $calculatedItems,
            'subtotal' => round($subtotal, 2),
            'discount' => round($invDiscountNum, 2),
            'tax' => round($taxNum, 2),
            'total' => round($total, 2),
        ];
    }
}
