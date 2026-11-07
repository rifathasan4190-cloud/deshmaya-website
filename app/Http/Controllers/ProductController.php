<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');

        return $this->listing(is_string($category) ? $category : null);
    }

    public function category(string $slug): View
    {
        $category = Category::query()
            ->active()
            ->whereNotNull('parent_id')
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->listing($category->slug);
    }

    private function listing(?string $requestedCategory): View
    {
        $categories = Category::query()
            ->active()
            ->with('media')
            ->whereNotNull('parent_id')
            ->ordered()
            ->get();

        $selectedCategory = $requestedCategory;
        $categoryExists = $categories->contains('slug', $selectedCategory);

        if (! $categoryExists) {
            $selectedCategory = null;
        }

        $products = Product::query()
            ->active()
            ->with(['category', 'media'])
            ->when($selectedCategory, fn ($query) => $query->whereHas(
                'category',
                fn ($categoryQuery) => $categoryQuery->where('slug', $selectedCategory),
            ))
            ->ordered()
            ->get();

        return view('pages.products.index', compact('categories', 'products', 'selectedCategory'));
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->with(['category.parent', 'media'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->active()
            ->with(['category', 'media'])
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
            ->ordered()
            ->limit(8)
            ->get();

        $currentMonth = now()->month;
        $demoSeasonMonths = range(1, 12);
        $inSeasonProducts = Product::query()
            ->active()
            ->with(['category', 'media'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->ordered()
            ->get()
            ->filter(function (Product $candidate) use ($currentMonth, $demoSeasonMonths): bool {
                $seasonMonths = filled($candidate->season_months)
                    ? array_map('intval', $candidate->season_months)
                    : $demoSeasonMonths;

                return in_array($currentMonth, $seasonMonths, true);
            })
            ->take(8)
            ->values();

        return view('pages.products.show', compact(
            'product',
            'relatedProducts',
            'inSeasonProducts',
            'currentMonth',
            'demoSeasonMonths',
        ));
    }
}
