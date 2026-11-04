<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fruitCategory = Category::query()->where('slug', 'fruits')->firstOrFail();
        $vegetableCategory = Category::query()->where('slug', 'vegetables')->firstOrFail();

        Product::updateOrCreate(
            ['slug' => 'fresh-mangoes'],
            [
                'category_id' => $fruitCategory->id,
                'name' => 'Fresh Mangoes',
                'short_description' => 'Premium seasonal mangoes prepared for export.',
                'description' => 'Carefully selected mangoes packed to preserve freshness during transit.',
                'specs' => ['Origin' => 'Bangladesh', 'Packaging' => 'Cartons', 'Season' => 'Seasonal'],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        Product::updateOrCreate(
            ['slug' => 'fresh-bananas'],
            [
                'category_id' => $fruitCategory->id,
                'name' => 'Fresh Bananas',
                'short_description' => 'Fresh bananas sorted for international buyers.',
                'description' => 'Export-ready bananas handled and packed for reliable delivery.',
                'specs' => ['Origin' => 'Bangladesh', 'Packaging' => 'Ventilated cartons'],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
        );

        Product::updateOrCreate(
            ['slug' => 'red-onions'],
            [
                'category_id' => $vegetableCategory->id,
                'name' => 'Red Onions',
                'short_description' => 'Grade-sorted red onions for wholesale export.',
                'description' => 'Firm red onions sorted by size and packed to buyer specifications.',
                'specs' => ['Origin' => 'Bangladesh', 'Packaging' => 'Mesh bags'],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
        );
    }
}
