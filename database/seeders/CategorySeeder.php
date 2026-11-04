<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produce = Category::updateOrCreate(
            ['slug' => 'fresh-produce'],
            [
                'name' => 'Fresh Produce',
                'description' => 'Fresh agricultural products sourced for export.',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        Category::updateOrCreate(
            ['slug' => 'fruits'],
            [
                'parent_id' => $produce->id,
                'name' => 'Fruits',
                'description' => 'Fresh seasonal fruits for global markets.',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        Category::updateOrCreate(
            ['slug' => 'vegetables'],
            [
                'parent_id' => $produce->id,
                'name' => 'Vegetables',
                'description' => 'Fresh vegetables prepared for export.',
                'sort_order' => 2,
                'is_active' => true,
            ],
        );
    }
}
