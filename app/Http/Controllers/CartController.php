<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    public function index()
    {
        $cart = $this->getCart();
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

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'numeric', 'min:0.01'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Stok habis.'], 422);
            }
            return back()->withErrors(['qty' => 'Stok habis.']);
        }

        // Validate pcs unit: must be integer
        if ($product->unit === 'pcs' && floor($request->qty) != $request->qty) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Jumlah harus bilangan bulat untuk satuan pcs.'], 422);
            }
            return back()->withErrors(['qty' => 'Jumlah harus bilangan bulat untuk satuan pcs.']);
        }

        $qty = (float) $request->qty;
        $qty = min($qty, (float) $product->stock); // cap at available stock

        $cart = $this->getCart();
        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $qty;
            $cart[$product->id]['qty'] = min($cart[$product->id]['qty'], (float) $product->stock);
        } else {
            $cart[$product->id] = ['qty' => $qty];
        }
        $this->saveCart($cart);

        $cartCount = count($cart);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$product->name} ditambahkan ke keranjang.",
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'numeric', 'min:0'],
        ]);

        $cart = $this->getCart();
        $productId = $request->product_id;

        if ($request->qty <= 0) {
            unset($cart[$productId]);
        } else {
            $product = Product::find($productId);
            if ($product) {
                $qty = (float) $request->qty;
                $cart[$productId] = ['qty' => min($qty, (float) $product->stock)];
            }
        }

        $this->saveCart($cart);
        return redirect()->route('cart.index');
    }

    public function remove(int $productId)
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->saveCart($cart);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function count()
    {
        return response()->json(['count' => count(session('cart', []))]);
    }
}
