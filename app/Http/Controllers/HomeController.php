<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $carouselProducts = Product::query()
            ->active()
            ->with('media')
            ->ordered()
            ->limit(12)
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

        return view('pages.home', compact('carouselProducts', 'featuredProducts'));
    }
}
