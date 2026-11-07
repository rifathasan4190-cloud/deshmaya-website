<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Categories\Pages\CreateCategory;
use App\Filament\Admin\Resources\Categories\Pages\EditCategory;
use App\Filament\Admin\Resources\Products\Pages\CreateProduct;
use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_slugs_are_normalized_and_unique_even_after_soft_delete(): void
    {
        $category = Category::query()->create(['name' => 'Fresh Mangoes']);
        $duplicateCategory = Category::query()->create(['name' => 'Fresh Mangoes']);

        $this->assertSame('fresh-mangoes', $category->slug);
        $this->assertSame('fresh-mangoes-2', $duplicateCategory->slug);

        $category->delete();

        $thirdCategory = Category::query()->create(['name' => 'Fresh Mangoes']);
        $this->assertSame('fresh-mangoes-3', $thirdCategory->slug);

        $productsCategory = Category::query()->create(['name' => 'Produce']);
        $product = Product::query()->create([
            'category_id' => $productsCategory->id,
            'name' => 'Organic Red Onion',
        ]);
        $duplicateProduct = Product::query()->create([
            'category_id' => $productsCategory->id,
            'name' => 'Organic Red Onion',
        ]);

        $this->assertSame('organic-red-onion', $product->slug);
        $this->assertSame('organic-red-onion-2', $duplicateProduct->slug);
    }

    public function test_catalog_relationships_scopes_specs_and_media_collections(): void
    {
        $parent = Category::query()->create(['name' => 'Fresh Produce']);
        $fruits = Category::query()->create([
            'name' => 'Fruits',
            'parent_id' => $parent->id,
            'sort_order' => 1,
        ]);
        $vegetables = Category::query()->create([
            'name' => 'Vegetables',
            'parent_id' => $parent->id,
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $featuredProduct = Product::query()->create([
            'category_id' => $fruits->id,
            'name' => 'Export Mangoes',
            'specs' => ['Origin' => 'Bangladesh', 'Packaging' => 'Carton'],
            'is_featured' => true,
            'sort_order' => 1,
        ]);
        Product::query()->create([
            'category_id' => $fruits->id,
            'name' => 'Inactive Fruit',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $this->assertSame($parent->id, $fruits->parent->id);
        $this->assertEquals([$fruits->id, $vegetables->id], $parent->children()->pluck('id')->all());
        $this->assertTrue($fruits->products()->whereKey($featuredProduct->id)->exists());
        $this->assertSame(['Origin' => 'Bangladesh', 'Packaging' => 'Carton'], $featuredProduct->specs);
        $this->assertEquals(['Fresh Produce', 'Fruits'], Category::query()->active()->ordered()->pluck('name')->all());
        $this->assertSame(['Export Mangoes'], Product::query()->active()->featured()->ordered()->pluck('name')->all());
        $this->assertEqualsCanonicalizing(['image'], $fruits->getRegisteredMediaCollections()->pluck('name')->all());
        $this->assertEqualsCanonicalizing(['featured', 'gallery'], $featuredProduct->getRegisteredMediaCollections()->pluck('name')->all());
    }

    public function test_catalog_seeders_are_repeatable(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, Category::query()->count());
        $this->assertSame(3, Product::query()->count());
        $this->assertSame(1, Product::query()->featured()->count());
    }

    public function test_category_parent_cannot_create_a_cycle(): void
    {
        $parent = Category::query()->create(['name' => 'Parent Category']);
        $child = Category::query()->create([
            'name' => 'Child Category',
            'parent_id' => $parent->id,
        ]);

        $parent->parent_id = $child->id;

        $this->expectException(ValidationException::class);
        $parent->save();
    }

    public function test_admin_can_create_categories_and_products_in_filament(): void
    {
        $admin = User::query()->create([
            'name' => 'Catalog Admin',
            'email' => 'catalog-admin@example.com',
            'password' => 'test-password',
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(CreateCategory::class)
            ->fillForm([
                'name' => 'Citrus Fruits',
                'slug' => '',
                'description' => 'Fresh citrus for export.',
                'sort_order' => 3,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $category = Category::query()->where('slug', 'citrus-fruits')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(CreateProduct::class)
            ->fillForm([
                'name' => 'Export Oranges',
                'slug' => '',
                'category_id' => $category->id,
                'short_description' => 'Fresh oranges ready for export.',
                'description' => 'Carefully sorted and packed oranges.',
                'specs' => ['Origin' => 'Bangladesh', 'Packaging' => 'Carton'],
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'slug' => 'export-oranges',
            'category_id' => $category->id,
        ]);
    }

    public function test_product_form_saves_featured_and_gallery_media(): void
    {
        Storage::fake('public');

        $admin = User::query()->create([
            'name' => 'Catalog Admin',
            'email' => 'media-admin@example.com',
            'password' => 'test-password',
        ]);
        $admin->forceFill(['is_admin' => true])->save();
        $category = Category::query()->create(['name' => 'Media Category']);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(CreateProduct::class)
            ->fillForm([
                'name' => 'Product with Media',
                'slug' => 'product-with-media',
                'category_id' => $category->id,
                'featured_image' => UploadedFile::fake()->image('featured.jpg', 20, 20),
                'gallery' => [UploadedFile::fake()->image('gallery.jpg', 20, 20)],
                'specs' => [],
                'sort_order' => 0,
                'is_active' => true,
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $product = Product::query()->where('slug', 'product-with-media')->firstOrFail();
        $media = $product->getFirstMedia('featured');

        $this->assertSame(1, $product->getMedia('featured')->count());
        $this->assertSame(1, $product->getMedia('gallery')->count());
        $this->assertSame('public', $media->disk);
        $this->assertNotNull($product->getAvailableMediaUrl());

        Storage::disk($media->disk)->delete($media->getPathRelativeToRoot());
        Storage::disk($media->conversions_disk ?: $media->disk)->delete($media->getPathRelativeToRoot('preview'));

        $this->assertNull($product->getAvailableMediaUrl());
    }

    public function test_admin_can_edit_and_soft_delete_catalog_records(): void
    {
        $admin = User::query()->create([
            'name' => 'Catalog Admin',
            'email' => 'edit-admin@example.com',
            'password' => 'test-password',
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        $category = Category::query()->create(['name' => 'Original Category']);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Original Product',
            'specs' => ['Origin' => 'Bangladesh'],
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditCategory::class, ['record' => $category->getKey()])
            ->fillForm([
                'name' => 'Updated Category',
                'slug' => 'updated-category',
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->call('save')
            ->assertHasNoErrors();

        Livewire::actingAs($admin)
            ->test(EditProduct::class, ['record' => $product->getKey()])
            ->fillForm([
                'name' => 'Updated Product',
                'slug' => 'updated-product',
                'category_id' => $category->id,
                'specs' => ['Origin' => 'Bangladesh', 'Packaging' => 'Carton'],
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'slug' => 'updated-category']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'slug' => 'updated-product']);

        Livewire::actingAs($admin)
            ->test(EditCategory::class, ['record' => $category->getKey()])
            ->callAction('delete');

        Livewire::actingAs($admin)
            ->test(EditProduct::class, ['record' => $product->getKey()])
            ->callAction('delete');

        $this->assertSoftDeleted($category);
        $this->assertSoftDeleted($product);
    }
}
