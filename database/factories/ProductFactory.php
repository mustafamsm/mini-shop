<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            $name = fake()->words(3, true);
        return [
            'category_id'=>Category::factory(),
            'name'=>ucfirst($name),
            'slug'=>str($name)->slug().'-'.fake()->unique()->numberBetween(1,9999),
            'description'=>fake()->paragraph(),
            'base_price'=>fake()->randomFloat(2,15,200),
            'is_active'=>true
        ];
    }
}
