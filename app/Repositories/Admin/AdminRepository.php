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
}