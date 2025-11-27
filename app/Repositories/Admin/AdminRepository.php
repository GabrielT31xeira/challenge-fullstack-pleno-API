<?php

namespace App\Repositories\Admin;

use App\Models\Order;
use App\Models\Product;

class AdminRepository
{
    public function dashboard()
    {
        $products = Product::where('active', 1)->count();
        $orders = Order::where('status', 'delivered')->count();
        $revenue = Order::where('status', 'delivered')->sum('total');

        return [$products, $orders, $revenue];
    }
}