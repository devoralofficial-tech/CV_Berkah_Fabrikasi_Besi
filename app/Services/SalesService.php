<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function __construct(private InventoryService $inventoryService) {}

    /**
     * Create a Walk-in sale (POS).
     *
     * @param array $items [['product_id'=>int,'qty'=>float], ...]
     */
    public function createWalkIn(
        array $items,
        ?string $customerName,
        string $paymentMethod,
        float $amountPaid,
        int $createdBy
    ): Sale {
        // Pre-validate all stock before touching DB
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->stock < $item['qty']) {
                throw new \RuntimeException(
                    "Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock} {$product->unit}"
                );
            }
        }

        return DB::transaction(function () use ($items, $customerName, $paymentMethod, $amountPaid, $createdBy) {
            $total = 0;
            $productMap = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $productMap[$item['product_id']] = $product;
                $total += $product->sell_price * $item['qty'];
            }

            $change = $paymentMethod === 'cash' ? max(0, $amountPaid - $total) : 0;

            $sale = Sale::create([
                'sale_number'    => $this->generateSaleNumber(),
                'order_id'       => null,
                'source'         => 'walk-in',
                'customer_name'  => $customerName ?: 'Umum',
                'total'          => $total,
                'payment_method' => $paymentMethod,
                'amount_paid'    => $amountPaid,
                'change'         => $change,
                'status'         => 'completed',
                'created_by'     => $createdBy,
            ]);

            foreach ($items as $item) {
                $product = $productMap[$item['product_id']];

                SaleItem::create([
                    'sale_id'             => $sale->id,
                    'product_id'          => $product->id,
                    'qty'                 => $item['qty'],
                    'unit_price_snapshot' => $product->sell_price,
                    'subtotal'            => $product->sell_price * $item['qty'],
                ]);

                $product->decrement('stock', $item['qty']);

                InventoryLog::create([
                    'product_id'   => $product->id,
                    'type'         => 'out',
                    'qty'          => $item['qty'],
                    'source'       => 'sale',
                    'reference_id' => $sale->id,
                    'note'         => "Penjualan walk-in #{$sale->sale_number}",
                    'created_by'   => $createdBy,
                ]);
            }

            return $sale;
        });
    }

    /**
     * Complete an online order — creates a Sale, decrements stock.
     */
    public function completeOrder(Order $order, int $createdBy): Sale
    {
        // Pre-validate stock
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if (!$product || $product->stock < $item->qty) {
                $available = $product ? $product->stock : 0;
                throw new \RuntimeException(
                    "Stok {$item->product->name} tidak mencukupi. Tersedia: {$available}, Dibutuhkan: {$item->qty}"
                );
            }
        }

        return DB::transaction(function () use ($order, $createdBy) {
            $sale = Sale::create([
                'sale_number'    => $this->generateSaleNumber(),
                'order_id'       => $order->id,
                'source'         => 'online',
                'customer_name'  => $order->customer_name,
                'total'          => $order->total_estimate,
                'payment_method' => $order->payment_method,
                'amount_paid'    => 0,
                'change'         => 0,
                'status'         => 'completed',
                'created_by'     => $createdBy,
            ]);

            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                SaleItem::create([
                    'sale_id'             => $sale->id,
                    'product_id'          => $product->id,
                    'qty'                 => $item->qty,
                    'unit_price_snapshot' => $item->unit_price_snapshot,
                    'subtotal'            => $item->subtotal,
                ]);

                $product->decrement('stock', $item->qty);

                InventoryLog::create([
                    'product_id'   => $product->id,
                    'type'         => 'out',
                    'qty'          => $item->qty,
                    'source'       => 'sale',
                    'reference_id' => $sale->id,
                    'note'         => "Penjualan online order #{$order->order_number}",
                    'created_by'   => $createdBy,
                ]);
            }

            $order->update(['status' => 'completed']);

            return $sale;
        });
    }

    /**
     * Void a completed sale — reverses stock, inserts log.
     */
    public function voidSale(Sale $sale, int $createdBy): Sale
    {
        if ($sale->status !== 'completed') {
            throw new \RuntimeException('Hanya transaksi berstatus "completed" yang dapat di-void.');
        }

        return DB::transaction(function () use ($sale, $createdBy) {
            $sale->update(['status' => 'voided']);

            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $product->increment('stock', $item->qty);

                InventoryLog::create([
                    'product_id'   => $product->id,
                    'type'         => 'in',
                    'qty'          => $item->qty,
                    'source'       => 'void',
                    'reference_id' => $sale->id,
                    'note'         => "Void transaksi #{$sale->sale_number}",
                    'created_by'   => $createdBy,
                ]);
            }

            return $sale->fresh();
        });
    }

    /**
     * Generate sequential sale number: INV-YYYY-NNNNN
     */
    public function generateSaleNumber(): string
    {
        $year = now()->format('Y');
        $last = Sale::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $seq = $last ? (int) substr($last->sale_number, -5) + 1 : 1;
        return 'INV-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}
