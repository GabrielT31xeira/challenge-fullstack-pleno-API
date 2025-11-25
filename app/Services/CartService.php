<?php

namespace App\Services;

use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;

class CartService
{
    public function __construct(
        protected CartRepository $cartsRepository,
        protected ProductRepository $productRepository
    ) {}

    public function getOne($id)
    {
        return $this->cartsRepository->getOne($id);
    }

    public function getAll(string $userId)
    {
        return $this->cartsRepository->getAll($userId);
    }

    public function getCart(array $filters, string $userId)
    {
        return $this->cartsRepository->getUserCarts($filters, $userId);
    }

    public function createCart($cartData, $userId)
    {
        return $this->cartsRepository->createCart($cartData, $userId);
    }

    public function addItem($request, $user_id)
    {
        $product = $this->productRepository->find($request['product_id']);
        if (!$product) {
            throw new \Exception("Produto não encontrado");
        }

        $available = $product->quantity - $product->min_quantity;
        if ($request['quantity'] > $available) {
            throw new \Exception("Quantidade indisponível no estoque");
        }

        $this->productRepository->update($product->id, [
            'quantity' => $product->quantity - $request['quantity']
        ]);

        return $this->cartsRepository->addItem($request, $user_id);
    }

    public function updateItem(string $cartId, string $itemId, int $quantity)
    {
        $cart = $this->cartsRepository->getOne($cartId);

        if (!$cart) {
            throw new \Exception("Carrinho não encontrado");
        }

        $item = $cart->items()->where('product_id', $itemId)->firstOrFail();

        $item->update([
            'quantity' => $quantity
        ]);

        return $item;
    }

    public function removeItem(string $cartId, string $itemId)
    {
        $cart = $this->cartsRepository->getOne($cartId);

        if (!$cart) {
            throw new \Exception("Carrinho não encontrado");
        }

        $item = $cart->items()->where('product_id', $itemId)->firstOrFail();

        return $item->delete();
    }

    public function clear(string $cartId)
    {
        $cart = $this->cartsRepository->getOne($cartId);

        if (!$cart) {
            throw new \Exception("Carrinho não encontrado");
        }

        return $cart->items()->delete();
    }
}
