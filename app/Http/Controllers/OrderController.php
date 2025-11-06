<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product', 'address', 'payment')
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        $cart = Cart::firstOrCreate(
            ['user_id' => $request->user()->id, 'is_active' => true]
        );
        $cart->load('items.product');


        if ($cart->items->isEmpty()) {
            return response()->json(['error' => 'Carrinho vazio'], 400);
        }

        $total = $cart->items->sum(function($item) {
            return $item->quantity * $item->price;
        });

        $order = Order::create([
            'user_id' => $request->user()->id,
            'address_id' => $request->address_id,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price
            ]);
        }

        // limpa carrinho
        $cart->items()->delete();
        $cart->update(['is_active' => false]);

        return response()->json($order->load('items.product'), 201);
    }

    public function show(Order $order)
    {
        return response()->json($order->load('items.product.specifications', 'address', 'payment','user'));
    }
}
