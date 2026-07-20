<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new Order from the session cart.
     * Validates stock server-side. Does NOT decrement stock.
     *
     * @param array $cartItems  [['product_id' => int, 'qty' => float], ...]
     * @param array $customerData
     * @return Order
     * @throws \RuntimeException on stock validation failure
     */
    public function createFromCart(array $cartItems, array $customerData): Order
    {
        // Pre-validate stock for all items
        $stockErrors = [];
        $productMap  = [];

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                $stockErrors[] = "Produk tidak ditemukan (ID: {$item['product_id']})";
                continue;
            }
            if ($product->stock < $item['qty']) {
                $stockErrors[] = "Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock} {$product->unit}, Diminta: {$item['qty']} {$product->unit}";
            }
            $productMap[$product->id] = $product;
        }

        if (!empty($stockErrors)) {
            throw new \RuntimeException(implode("\n", $stockErrors));
        }

        return DB::transaction(function () use ($cartItems, $customerData, $productMap) {
            $orderNumber = $this->generateOrderNumber();

            $totalEstimate = 0;
            foreach ($cartItems as $item) {
                $totalEstimate += $productMap[$item['product_id']]->sell_price * $item['qty'];
            }

            $order = Order::create([
                'order_number'    => $orderNumber,
                'customer_name'   => $customerData['customer_name'],
                'customer_phone'  => $customerData['customer_phone'],
                'customer_address' => $customerData['customer_address'] ?? null,
                'payment_method'  => $customerData['payment_method'],
                'status'          => 'pending',
                'total_estimate'  => $totalEstimate,
            ]);

            foreach ($cartItems as $item) {
                $product = $productMap[$item['product_id']];
                OrderItem::create([
                    'order_id'            => $order->id,
                    'product_id'          => $product->id,
                    'qty'                 => $item['qty'],
                    'unit_price_snapshot' => $product->sell_price,
                    'subtotal'            => $product->sell_price * $item['qty'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Update order status, enforcing linear transition rules.
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        if (!$order->canTransitionTo($newStatus)) {
            throw new \RuntimeException(
                "Transisi status dari '{$order->status}' ke '{$newStatus}' tidak diizinkan."
            );
        }

        $order->update(['status' => $newStatus]);
        return $order->fresh();
    }

    /**
     * Generate sequential order number: ORD-YYYY-NNNNN
     */
    public function generateOrderNumber(): string
    {
        $year  = now()->format('Y');
        $last  = Order::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $seq = $last ? (int) substr($last->order_number, -5) + 1 : 1;
        return 'ORD-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Build WhatsApp redirect URL from an order.
     */
    public function buildWhatsappUrl(Order $order): string
    {
        $setting = Setting::getSetting();
        $waNumber = preg_replace('/[^0-9]/', '', $setting->wa_number);

        $lines = ["Halo, saya ingin memesan (Order #{$order->order_number}):"];
        $order->loadMissing('items.product');

        foreach ($order->items as $i => $item) {
            $productName = $item->product ? $item->product->name : 'Produk';
            $unit        = $item->product ? $item->product->unit : '';
            $lines[] = ($i + 1) . ". {$productName} — {$item->qty} {$unit}";
        }

        $total  = 'Rp ' . number_format($order->total_estimate, 0, ',', '.');
        $lines[] = "Total estimasi: {$total}";
        $lines[] = '';
        $lines[] = "Nama: {$order->customer_name}";
        $lines[] = "No. HP: {$order->customer_phone}";
        $lines[] = "Alamat: " . ($order->customer_address ?: '-');

        $message = implode("\n", $lines);
        return "https://wa.me/{$waNumber}?text=" . rawurlencode($message);
    }
}
