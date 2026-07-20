<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->sell_price * $item['qty'];
                $total += $subtotal;
                $cartItems[] = [
                    'product'  => $product,
                    'qty'      => $item['qty'],
                    'subtotal' => $subtotal,
                ];
            }
        }

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function store(CheckoutRequest $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Build items array
        $cartItems = [];
        foreach ($cart as $productId => $item) {
            $cartItems[] = ['product_id' => $productId, 'qty' => $item['qty']];
        }

        try {
            $order = $this->orderService->createFromCart($cartItems, $request->validated());
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Stok tidak mencukupi: ' . $e->getMessage());
        }

        // Clear cart
        session()->forget('cart');

        // Redirect to WhatsApp
        $waUrl = $this->orderService->buildWhatsappUrl($order);
        return redirect()->away($waUrl);
    }
}
