<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->active()
            ->with('media')
            ->whereNotNull('parent_id')
            ->ordered()
            ->get();

        $featuredProducts = Product::query()
            ->active()
            ->with(['category', 'media'])
            ->featured()
            ->ordered()
            ->limit(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::query()
                ->active()
                ->with(['category', 'media'])
                ->ordered()
                ->limit(8)
                ->get();
        }

        return view('pages.home', compact('categories', 'featuredProducts'));
    }
}
