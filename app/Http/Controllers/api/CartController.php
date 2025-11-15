<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function show(Request $request)
    {
        $cart = $this->cart->getCart($request->user()->id);

        return response()->json([
            'message' => 'Carrinho carregado com sucesso.',
            'cart' => $cart,
        ]);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $item = $this->cart->addItem(
            $request->user()->id,
            $validated['product_id'],
            $validated['quantity']
        );

        return response()->json([
            'message' => 'Item adicionado ao carrinho.',
            'item' => $item
        ], 201);
    }

    public function updateItem(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        $item = $this->cart->updateItem(
            $request->user()->id,
            $id,
            $validated['quantity']
        );

        return response()->json([
            'message' => 'Item atualizado com sucesso.',
            'item' => $item
        ]);
    }

    public function removeItem(Request $request, $id)
    {
        $this->cart->removeItem($request->user()->id, $id);

        return response()->json([
            'message' => 'Item removido do carrinho.',
        ]);
    }

    public function clear(Request $request)
    {
        $this->cart->clear($request->user()->id);

        return response()->json([
            'message' => 'Carrinho limpo com sucesso.',
        ]);
    }
}
