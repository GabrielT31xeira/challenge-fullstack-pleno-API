<?php

namespace App\Repositories\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\Tag;

class AdminRepository
{
    public function dashboard()
    {
        $products = Product::where('active', 1)->count();
        $orders = Order::where('status', 'delivered')->count();
        $revenue = Order::where('status', 'delivered')->sum('total');

        return [$products, $orders, $revenue];
    }

    public function getAllTags()
    {
        return Tag::all();
    }

    public function lowStock()
    {
        return Product::whereColumn('quantity', '<=', 'min_quantity')
            ->orderBy('quantity', 'desc')
            ->paginate(9);
    }

    public function orders($data)
    {
        $query = Order::with(['cart.items.product', 'items.product']);

        // Filtro por nome (search)
        if (!empty($data['search'])) {
            $query->whereHas('cart', function ($q) use ($data) {
                $q->where('name', 'like', "%{$data['search']}%");
            });
        }

        // Filtro por status
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        // (Opcional) Filtro por preço máximo
        if (!empty($data['max_price'])) {
            $query->where('total', '<=', $data['max_price']);
        }

        return $query->latest()->paginate(9);
    }

}