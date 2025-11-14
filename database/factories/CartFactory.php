<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CartFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::inRandomOrder()->first()->id,
            'session_id' => Str::uuid(),
        ];
    }
}
