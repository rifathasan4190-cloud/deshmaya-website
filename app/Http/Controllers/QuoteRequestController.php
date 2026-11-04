<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Services\SubmissionNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    public function create(Request $request): View
    {
        $products = Product::query()
            ->active()
            ->ordered()
            ->get(['id', 'name', 'slug']);

        $selectedProductId = $request->filled('product')
            ? $products->firstWhere('slug', $request->query('product'))?->id
            : null;

        return view('pages.request-quote', [
            'products' => $products,
            'selectedProductId' => $selectedProductId,
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ]);
    }

    public function store(
        StoreQuoteRequest $request,
        SubmissionNotifier $notifier,
    ): RedirectResponse {
        $recipient = $notifier->recipient();
        $data = $request->safe()->only([
            'name', 'company', 'email', 'phone', 'country', 'product_id', 'quantity', 'unit', 'message',
        ]);
        $product = filled($data['product_id'] ?? null)
            ? Product::query()->active()->findOrFail($data['product_id'])
            : null;

        QuoteRequest::query()->create($data + [
            'product_name' => $product?->name,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
                ? Str::limit($request->userAgent(), 1000, '')
                : null,
        ]);

        $data['product_name'] = $product?->name;
        unset($data['product_id']);
        $notifier->notify($recipient, 'quote request', $data);

        return to_route('request-quote')->with('status', 'Thank you. Your quote request has been received.');
    }
}
