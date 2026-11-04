<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_all_static_public_pages_render(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Delivered to the');
        $this->get(route('about'))->assertOk()->assertSee('Growing Bangladesh');
        $this->get(route('certification'))->assertOk()->assertSee('Documentation for every shipment');
        $this->get(route('how-we-export'))->assertOk()->assertSee('From farm to global market');
        $this->get(route('export-markets'))->assertOk()->assertSee('Our Market Reach');
        $this->get(route('quality-packaging'))->assertOk()->assertSeeText('Quality & Packaging');
    }

    public function test_static_pages_have_single_main_heading_and_seo_metadata(): void
    {
        foreach (['home', 'about', 'products.index', 'certification', 'quality-packaging', 'how-we-export', 'export-markets', 'contact', 'request-quote'] as $routeName) {
            $response = $this->get(route($routeName))->assertOk();
            $html = $response->getContent();

            $this->assertSame(1, preg_match_all('/<h1(?:\s|>)/i', $html));
            $this->assertStringContainsString('rel="canonical"', $html);
            $this->assertStringContainsString('property="og:url"', $html);
            $this->assertStringContainsString('name="description"', $html);
            $this->assertStringContainsString('"@type":"Organization"', $html);
        }
    }

    public function test_robots_and_sitemap_include_public_routes_and_active_catalog_items(): void
    {
        $category = Category::create([
            'name' => 'Fruits',
            'slug' => 'fruits',
            'parent_id' => Category::create([
                'name' => 'Produce',
                'slug' => 'produce',
                'is_active' => true,
            ])->id,
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fresh Mango',
            'slug' => 'fresh-mango',
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Hidden Mango',
            'slug' => 'hidden-mango',
            'is_active' => false,
        ]);

        $this->get(route('robots'))
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee(route('sitemap'));

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('home'), false)
            ->assertSee(route('products.category', $category->slug), false)
            ->assertSee(route('products.show', $product->slug), false)
            ->assertDontSee('hidden-mango');
    }

    public function test_public_product_page_includes_product_structured_data(): void
    {
        $category = Category::create([
            'name' => 'Fruits',
            'slug' => 'fruits',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fresh Mango',
            'slug' => 'fresh-mango',
            'short_description' => 'Export quality mango.',
            'is_active' => true,
        ]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('"@type":"Product"', false)
            ->assertSee('"name":"Fresh Mango"', false);
    }

    public function test_public_pages_have_security_headers_and_https_hsts(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_custom_not_found_and_server_error_views_render(): void
    {
        $notFound = $this->get('/page-that-does-not-exist')
            ->assertNotFound()
            ->assertSee('Page not found');
        $this->assertSame(1, preg_match_all('/<h1(?:\s|>)/i', $notFound->getContent()));

        $serverError = view('errors.500')->render();
        $this->assertStringContainsString('Something went wrong', $serverError);
        $this->assertSame(1, preg_match_all('/<h1(?:\s|>)/i', $serverError));
    }

    public function test_products_list_and_single_product_details_are_dynamic(): void
    {
        $parentCategory = Category::create([
            'name' => 'Fresh Produce',
            'slug' => 'fresh-produce',
            'is_active' => true,
        ]);
        $category = Category::create([
            'parent_id' => $parentCategory->id,
            'name' => 'Fruits',
            'slug' => 'fruits',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fresh Mango',
            'slug' => 'fresh-mango',
            'short_description' => 'Premium seasonal mangoes for export.',
            'description' => 'Selected and packed to buyer requirements.',
            'specs' => ['Origin' => 'Bangladesh'],
            'is_active' => true,
            'is_featured' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Fresh Mango')
            ->assertSee(route('products.show', $product->slug), false);

        $this->get(route('products.category', $category->slug))
            ->assertOk()
            ->assertSee('Fresh Mango')
            ->assertSee('Premium seasonal mangoes for export.');

        $productResponse = $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('Selected and packed to buyer requirements.')
            ->assertSee('Bangladesh')
            ->assertSee(route('request-quote', ['product' => $product->slug]), false);

        $this->assertSame(1, preg_match_all('/<h1(?:\s|>)/i', $productResponse->getContent()));
    }

    public function test_inactive_products_are_not_publicly_visible(): void
    {
        $category = Category::create([
            'name' => 'Vegetables',
            'slug' => 'vegetables',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Hidden Product',
            'slug' => 'hidden-product',
            'is_active' => false,
        ]);

        $this->get(route('products.index'))->assertOk()->assertDontSee('Hidden Product');
        $this->get(route('products.show', $product->slug))->assertNotFound();
    }

    public function test_malformed_product_filter_falls_back_to_all_products(): void
    {
        $this->get(route('products.index').'?category[]=invalid')->assertOk();
    }
}
