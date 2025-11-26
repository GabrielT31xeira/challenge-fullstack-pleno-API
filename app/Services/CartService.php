<?php

namespace App\Services;

use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($request, $user_id) {
            $product = $this->productRepository->find($request['product_id']);
            $this->validateStockAvailable($product, $request['quantity']);
            $this->productRepository->update($product->id, [
                'quantity' => $product->quantity - $request['quantity']
            ]);

            return $this->cartsRepository->addItem($request, $user_id);
        });
    }

    public function updateItem(string $cartId, string $productId, int $newQuantity)
    {
        return DB::transaction(function () use ($cartId, $productId, $newQuantity) {
            $cart = $this->cartsRepository->getOne($cartId);
            if (!$cart) {
                throw new \Exception("Carrinho não encontrado");
            }

            $currentItem = $cart->items()->where('product_id', $productId)->first();
            if (!$currentItem) {
                throw new \Exception("O item não existe neste carrinho");
            }

            $oldQuantity = $currentItem->quantity;
            $diff = $newQuantity - $oldQuantity;
            $product = $this->productRepository->find($productId);
            if (!$product) {
                throw new \Exception("Produto não encontrado");
            }

            if ($diff > 0) {
                $this->validateStockAvailable($product, $diff);
                $product->update([
                    'quantity' => $product->quantity - $diff
                ]);

            } elseif ($diff < 0) {
                $product->update([
                    'quantity' => $product->quantity + abs($diff)
                ]);
            }

            return $this->cartsRepository->updateItem($productId, $cart, $newQuantity);
        });
    }


    public function removeItem(string $cartId, string $productId)
    {
        $cart = $this->cartsRepository->getOne($cartId);

        if (!$cart) {
            throw new \Exception("Carrinho não encontrado");
        }

        return $this->cartsRepository->removeItem($productId, $cart);
    }

    public function clear(string $cartId)
    {
        $cart = $this->cartsRepository->getOne($cartId);

        if (!$cart) {
            throw new \Exception("Carrinho não encontrado");
        }

        return $cart->items()->delete();
    }

    public function deleteCart(string $cartId)
    {
        return DB::transaction(function () use ($cartId) {
            $cart = $this->cartsRepository->getOne($cartId);

            if (!$cart) {
                throw new \Exception("Carrinho não encontrado");
            }

            $cart->items()->delete();
            $cart->delete();
            return true;
        });
    }


    private function validateStockAvailable($product, int $quantityNeeded)
    {
        $available = $product->quantity - $product->min_quantity;

        if ($quantityNeeded > $available) {
            throw new \Exception("Quantidade indisponível no estoque");
        }
    }

}
