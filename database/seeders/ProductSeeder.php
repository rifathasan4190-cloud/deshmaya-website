<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fruitCategory = Category::query()->where('slug', 'fruits')->firstOrFail();
        $vegetableCategory = Category::query()->where('slug', 'vegetables')->firstOrFail();

        $this->upsertProduct(
            $fruitCategory,
            'Fresh Mangoes',
            1,
            slug: 'fresh-mangoes',
            shortDescription: 'Premium seasonal mangoes prepared for export.',
            description: 'Carefully selected mangoes packed to preserve freshness during transit.',
            specs: ['Origin' => 'Bangladesh', 'Packaging' => 'Cartons', 'Season' => 'Seasonal'],
            featured: true,
        );

        $this->upsertProduct(
            $fruitCategory,
            'Fresh Bananas',
            2,
            slug: 'fresh-bananas',
            shortDescription: 'Fresh bananas sorted for international buyers.',
            description: 'Export-ready bananas handled and packed for reliable delivery.',
            specs: ['Origin' => 'Bangladesh', 'Packaging' => 'Ventilated cartons'],
        );

        $vegetables = [
            ['Green Beans', 'beans'],
            ['Beetroot', 'beetroot'],
            ['Bitter Gourd', 'bitter-gourd'],
            ['Bottle Gourd', 'bottle-gourd', '8f750c70fbd79006bc9ea8b4b56c96c7.jpg'],
            ['Broccoli', 'broccoli'],
            ['Cabbage', 'cabbage'],
            ['Capsicum', 'capsicum'],
            ['Carrot', 'carrot'],
            ['Cauliflower', 'cauliflower'],
            ['Corn', 'corn'],
            ['Cucumber', 'cucumber'],
            ['Drumstick', 'drumstick'],
            ['Eggplant', 'eggplant'],
            ['Green Chili', 'green-chili'],
            ['Green Peas', 'green-peas'],
            ['Hyacinth Beans', 'Hyacinth beans'],
            ['Okra', 'ladies finger'],
            ['Red Onions', 'Onion'],
            ['Pointed Gourd', 'pointed-gourd'],
            ['Potato', 'potato'],
            ['Pumpkin', 'pumpkin'],
            ['Radish', 'radish'],
            ['Green Papaya', 'raw-papaya'],
            ['Red Amaranth', 'Red amaranth'],
            ['Ridge Gourd', 'ridge-gourd'],
            ['Snake Gourd', 'snake-gourd'],
            ['Spinach', 'spinach'],
            ['Sweet Potato', 'sweet-potato'],
            ['Taro', 'taro'],
            ['Tomato', 'tomato'],
            ['Turnip', 'turnip'],
            ['Water Spinach', 'water-spinach'],
            ['Yardlong Beans', 'yardlong-beans'],
        ];

        foreach ($vegetables as $index => $vegetable) {
            [$name, $imageFolder] = $vegetable;

            $this->upsertProduct(
                $vegetableCategory,
                $name,
                $index + 1,
                imageFolder: $imageFolder,
                preferredImage: $vegetable[2] ?? null,
            );
        }
    }

    private function upsertProduct(
        Category $category,
        string $name,
        int $sortOrder,
        ?string $imageFolder = null,
        ?string $preferredImage = null,
        ?string $slug = null,
        ?string $shortDescription = null,
        ?string $description = null,
        ?array $specs = null,
        bool $featured = false,
    ): void {
        $product = Product::updateOrCreate(
            ['slug' => $slug ?? Str::slug($name)],
            [
                'category_id' => $category->id,
                'name' => $name,
                'short_description' => $shortDescription ?? "Fresh {$name} selected for export buyers.",
                'description' => $description ?? 'Carefully sourced and prepared to buyer specifications. Availability, grading and packaging are confirmed per order.',
                'specs' => $specs ?? ['Origin' => 'Bangladesh', 'Availability' => 'Confirmed per order'],
                'is_featured' => $featured,
                'is_active' => true,
                'sort_order' => $sortOrder,
            ],
        );

        if ($imageFolder !== null) {
            $this->attachSourceImages($product, $imageFolder, $preferredImage);
        }
    }

    private function attachSourceImages(Product $product, string $imageFolder, ?string $preferredImage = null): void
    {
        $folder = public_path('images/Vegetable list/'.$imageFolder);
        $images = array_values(array_filter(
            glob($folder.'/*') ?: [],
            fn (string $path): bool => is_file($path)
                && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['avif', 'jpg', 'jpeg', 'png', 'webp'], true),
        ));
        sort($images, SORT_NATURAL | SORT_FLAG_CASE);

        if ($images === []) {
            return;
        }

        if ($preferredImage !== null) {
            $preferredPath = collect($images)->first(
                fn (string $path): bool => basename($path) === $preferredImage,
            );

            if ($preferredPath === null) {
                throw new \RuntimeException("The preferred product image [{$preferredImage}] was not found in [{$folder}].");
            }

            $images = [$preferredPath, ...array_values(array_filter(
                $images,
                fn (string $path): bool => $path !== $preferredPath,
            ))];
        }

        $sourceFilenames = array_map('basename', $images);
        $featuredImage = $product->getFirstMedia('featured');
        $galleryImages = $product->getMedia('gallery');
        $preferredGalleryImage = $preferredImage === null
            ? null
            : $galleryImages->firstWhere('file_name', $preferredImage);

        if ($preferredGalleryImage && (! $featuredImage || $featuredImage->file_name !== $preferredImage)) {
            if ($featuredImage) {
                $featuredImage->collection_name = 'gallery';
                $featuredImage->order_column = ($galleryImages->max('order_column') ?? 0) + 1;
                $featuredImage->save();
            }

            $preferredGalleryImage->collection_name = 'featured';
            $preferredGalleryImage->order_column = 1;
            $preferredGalleryImage->save();

            return;
        }

        if ($featuredImage) {
            if (
                $preferredImage === null
                || ! in_array($featuredImage->file_name, $sourceFilenames, true)
                || $featuredImage->file_name === basename($images[0])
            ) {
                return;
            }

            $featuredImage->collection_name = 'gallery';
            $featuredImage->order_column = ($galleryImages->max('order_column') ?? 0) + 1;
            $featuredImage->save();
        }

        $product->addMedia($images[0])
            ->preservingOriginal()
            ->toMediaCollection('featured', 'public');

        foreach (array_slice($images, 1) as $image) {
            if ($galleryImages->contains('file_name', basename($image))) {
                continue;
            }

            $product->addMedia($image)
                ->preservingOriginal()
                ->toMediaCollection('gallery', 'public');
        }
    }
}
