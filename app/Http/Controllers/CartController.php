<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Requests\Cart\UpdatePaymentMethodRequest;
use App\Http\Requests\Cart\UpdateShippingMethodRequest;
use App\Models\Payment;

class CartController extends Controller
{
    /**
     * Retorna o carrinho do usuário logado.
     */
    public function index()
    {
        $user = auth()->user();

        $items = $user->cartItems()->with(['product.images', 'specifications'])->get();

        $total = $items->sum(fn($item) => $item->subtotal);

        // Garante a leitura do carrinho ativo para expor os métodos
        $cart = Cart::firstOrCreate(['user_id' => $user->id, 'is_active' => true]);

        // Verifica se existe pagamento pendente vinculado a este carrinho
        $hasPendingPayment = Payment::where('cart_id', $cart->id)
            ->where('deleted', false)
            ->whereIn('status', ['PENDING', 'OVERDUE'])
            ->exists();

        return response()->json([
            'items' => $items,
            'total' => $total,
            'payment_method' => $cart->payment_method,
            'shipping_method' => $cart->shipping_method,
            'has_pending_payment' => $hasPendingPayment,
        ]);
    }

    /**
     * Adiciona um item ao carrinho.
     */
    public function add(Request $request)
    {
        $user = auth()->user();

        // Handle nested product_id object format
        if (is_array($request->product_id) || is_object($request->product_id)) {
            $productId = is_array($request->product_id) ? ($request->product_id['productId'] ?? null) : $request->product_id->productId;
            $specifications = is_array($request->product_id) ? ($request->product_id['specifications'] ?? []) : ($request->product_id->specifications ?? []);
            $quantity = is_array($request->product_id) ? ($request->product_id['quantity'] ?? $request->quantity) : ($request->product_id->quantity ?? $request->quantity);

            $request->merge([
                'product_id' => $productId,
                'specifications' => $specifications,
                'quantity' => $quantity
            ]);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'specifications' => 'sometimes|array',
            'specifications.*.name' => 'required_with:specifications|string',
            'specifications.*.value' => 'required_with:specifications',
            'specifications.*.type' => 'sometimes|string',
            'specifications.*.unit' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Pega ou cria o carrinho ativo do usuário
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'is_active' => true]
        );

        // Verifica se já existe o item no carrinho com as mesmas especificações
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->whereHas('specifications', function ($q) use ($request) {
                if (empty($request->specifications)) {
                    $q->whereNull('id'); // Busca itens sem especificações
                } else {
                    foreach ($request->specifications as $spec) {
                        $q->where('name', $spec['name'])
                            ->where('value', $spec['value']);
                    }
                }
            }, '=', count($request->specifications ?? []))
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->price = $product->price;
            $cartItem->subtotal = $cartItem->quantity * $product->price;
            $cartItem->save();
        } else {
            $cartItem = $cart->items()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'subtotal' => $request->quantity * $product->price,
            ]);

            // Adiciona as especificações ao item do carrinho
            if (!empty($request->specifications)) {
                foreach ($request->specifications as $spec) {
                    $cartItem->specifications()->create([
                        'name' => $spec['name'],
                        'value' => $spec['value'],
                        'type' => $spec['type'] ?? 'text',
                        'unit' => $spec['unit'] ?? null,
                    ]);
                }
            }
        }

        return $this->index();
    }

    /**
     * Atualiza a quantidade de um item no carrinho.
     */
    public function update(Request $request, $itemId)
    {
        $user = auth()->user();

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'specifications' => 'sometimes|array',
            'specifications.*.name' => 'required_with:specifications|string',
            'specifications.*.value' => 'required_with:specifications',
            'specifications.*.type' => 'sometimes|string',
            'specifications.*.unit' => 'nullable|string',
        ]);

        $cartItem = $user->cartItems()->findOrFail($itemId);

        $cartItem->quantity = $request->quantity;
        $cartItem->price = $cartItem->product->price;
        $cartItem->subtotal = $cartItem->quantity * $cartItem->price;
        $cartItem->save();

        // Atualiza as especificações se fornecidas
        if ($request->has('specifications')) {
            // Remove as especificações existentes
            $cartItem->specifications()->delete();

            // Adiciona as novas especificações
            foreach ($request->specifications as $spec) {
                $cartItem->specifications()->create([
                    'name' => $spec['name'],
                    'value' => $spec['value'],
                    'type' => $spec['type'] ?? 'text',
                    'unit' => $spec['unit'] ?? null,
                ]);
            }
        }

        return $this->index();
    }

    /**
     * Remove um item do carrinho.
     */
    public function remove($itemId)
    {
        $user = auth()->user();

        $cartItem = $user->cartItems()->findOrFail($itemId);
        $cartItem->delete();

        return $this->index();
    }

    /**
     * Limpa o carrinho inteiro.
     */
    public function clear()
    {
        $user = auth()->user();
        $user->cartItems()->delete();

        // Limpa também os métodos de pagamento e envio do carrinho ativo
        $cart = Cart::firstOrCreate(['user_id' => $user->id, 'is_active' => true]);
        $cart->payment_method = null;
        $cart->shipping_method = null;
        $cart->save();

        return response()->json(['message' => 'Carrinho limpo com sucesso']);
    }

    /**
     * Atualiza o método de pagamento do carrinho ativo do usuário.
     */
    public function updatePaymentMethod(UpdatePaymentMethodRequest $request)
    {
        $user = auth()->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id, 'is_active' => true]);

        $cart->payment_method = $request->validated();
        $cart->save();

        return response()->json($cart->fresh());
    }

    /**
     * Atualiza o método de envio do carrinho ativo do usuário.
     */
    public function updateShippingMethod(UpdateShippingMethodRequest $request)
    {
        $user = auth()->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id, 'is_active' => true]);

        $cart->shipping_method = $request->validated();
        $cart->save();

        return response()->json($cart->fresh());
    }
}
