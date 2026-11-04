<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        return response(
            "User-agent: *\nDisallow: /admin\nSitemap: ".route('sitemap')."\n",
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }

    public function sitemap(): Response
    {
        $staticUrls = collect([
            'home',
            'about',
            'products.index',
            'certification',
            'quality-packaging',
            'how-we-export',
            'export-markets',
            'contact',
            'request-quote',
        ])->map(fn (string $name): array => [
            'url' => route($name),
            'last_modified' => null,
        ]);

        $categoryUrls = Category::query()
            ->active()
            ->whereNotNull('parent_id')
            ->orderBy('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn (Category $category): array => [
                'url' => route('products.category', $category->slug),
                'last_modified' => $category->updated_at,
            ]);

        $productUrls = Product::query()
            ->active()
            ->orderBy('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn (Product $product): array => [
                'url' => route('products.show', $product->slug),
                'last_modified' => $product->updated_at,
            ]);

        return response()
            ->view('seo.sitemap', ['urls' => $staticUrls->concat($categoryUrls)->concat($productUrls)])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
