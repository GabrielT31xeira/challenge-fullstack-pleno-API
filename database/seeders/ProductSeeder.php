<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Category::factory(5)->create();
        Tag::factory(10)->create();

        Product::factory(50)
            ->create()
            ->each(function ($product) {
                $product->tags()->attach(
                    Tag::inRandomOrder()->take(rand(1, 3))->get()
                );
            });
    }
}
