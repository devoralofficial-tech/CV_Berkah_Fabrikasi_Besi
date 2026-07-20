<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Stock In — adds stock and records inventory log.
     */
    public function stockIn(
        Product $product,
        float $qty,
        string $source,
        ?string $note,
        int $createdBy,
        ?float $costPrice = null,
        ?int $referenceId = null
    ): InventoryLog {
        return DB::transaction(function () use ($product, $qty, $source, $note, $createdBy, $costPrice, $referenceId) {
            $product->increment('stock', $qty);

            if ($costPrice !== null) {
                $product->update(['cost_price' => $costPrice]);
            }

            return InventoryLog::create([
                'product_id'   => $product->id,
                'type'         => 'in',
                'qty'          => $qty,
                'source'       => $source,
                'reference_id' => $referenceId,
                'note'         => $note,
                'created_by'   => $createdBy,
            ]);
        });
    }

    /**
     * Stock Out — decrements stock and records inventory log.
     * Throws \RuntimeException if stock would go negative.
     */
    public function stockOut(
        Product $product,
        float $qty,
        string $source,
        ?string $note,
        int $createdBy,
        ?int $referenceId = null
    ): InventoryLog {
        return DB::transaction(function () use ($product, $qty, $source, $note, $createdBy, $referenceId) {
            // Reload with lock to prevent race conditions
            $product = Product::lockForUpdate()->findOrFail($product->id);

            if ($product->stock < $qty) {
                throw new \RuntimeException(
                    "Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}, Dibutuhkan: {$qty}"
                );
            }

            $product->decrement('stock', $qty);

            return InventoryLog::create([
                'product_id'   => $product->id,
                'type'         => 'out',
                'qty'          => $qty,
                'source'       => $source,
                'reference_id' => $referenceId,
                'note'         => $note,
                'created_by'   => $createdBy,
            ]);
        });
    }

    /**
     * Process Stock Opname — reconciles physical vs system stock.
     */
    public function processOpname(array $items, string $opnameDate, ?string $note, int $createdBy): StockOpname
    {
        return DB::transaction(function () use ($items, $opnameDate, $note, $createdBy) {
            $opname = StockOpname::create([
                'opname_date' => $opnameDate,
                'note'        => $note,
                'created_by'  => $createdBy,
            ]);

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $systemStock   = (float) $product->stock;
                $physicalStock = (float) $item['physical_stock'];
                $difference    = $physicalStock - $systemStock;

                StockOpnameItem::create([
                    'opname_id'      => $opname->id,
                    'product_id'     => $product->id,
                    'system_stock'   => $systemStock,
                    'physical_stock' => $physicalStock,
                    'difference'     => $difference,
                ]);

                if ($difference != 0) {
                    if ($difference > 0) {
                        $product->increment('stock', abs($difference));
                        InventoryLog::create([
                            'product_id'   => $product->id,
                            'type'         => 'in',
                            'qty'          => abs($difference),
                            'source'       => 'opname',
                            'reference_id' => $opname->id,
                            'note'         => 'Penyesuaian opname: +' . abs($difference),
                            'created_by'   => $createdBy,
                        ]);
                    } else {
                        $product->decrement('stock', abs($difference));
                        InventoryLog::create([
                            'product_id'   => $product->id,
                            'type'         => 'out',
                            'qty'          => abs($difference),
                            'source'       => 'opname',
                            'reference_id' => $opname->id,
                            'note'         => 'Penyesuaian opname: -' . abs($difference),
                            'created_by'   => $createdBy,
                        ]);
                    }
                }
            }

            return $opname;
        });
    }
}
