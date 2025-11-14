<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\StockMovement;

class StockSeeder extends Seeder
{
    public function run()
    {
        StockMovement::factory(100)->create();
    }
}
