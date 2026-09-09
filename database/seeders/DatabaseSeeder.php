<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        User::factory(10)->create();

        $user =  User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ]);
        $user->assignRole('admin');
        Category::factory(6)->create()->each(function ($category) {
            Product::factory(8)->create(['category_id' => $category->id])
                ->each(fn($product) => ProductVariant::factory(2)->create(['product_id' => $product->id]));
        });
    }
}
