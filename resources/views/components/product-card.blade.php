@props(['product', 'quickView' => false])

@php
    $image = $product->getAvailableMediaUrl();
    $quickViewData = $quickView ? [
        'name' => $product->name,
        'category' => $product->category?->name ?? 'Fresh produce',
        'image' => $image,
        'shortDescription' => $product->short_description,
        'description' => $product->description ?: $product->short_description,
        'specs' => $product->specs ?? [],
        'productUrl' => route('products.show', $product->slug),
        'quoteUrl' => route('request-quote', ['product' => $product->slug]),
    ] : null;
@endphp

<article @class(['product-card', 'product-card--minimal' => $quickView, 'hover-lift' => ! $quickView]) data-reveal>
    <div class="product-card__media">
        <a class="product-card__image-link" href="{{ route('products.show', $product->slug) }}" aria-label="View {{ $product->name }}">
            @if($image)
                <img class="product-card__image" src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
            @else
                <x-picture class="product-card__image" :src="'/images/products/'.$product->slug.'.jpg'" :alt="$product->name" />
            @endif
        </a>
        @if($quickView)
            <button
                class="quick-view-button"
                type="button"
                aria-haspopup="dialog"
                aria-label="Quick view {{ $product->name }}"
                @click="openQuickView({{ Illuminate\Support\Js::from($quickViewData) }}, $event)"
            >Quick View <span aria-hidden="true">→</span></button>
        @endif
    </div>
    <div class="product-card__body">
        <h3><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h3>
        @unless($quickView)
            <p class="product-card__category">{{ $product->category?->name ?? 'Fresh produce' }}</p>
            @if($product->short_description)
                <p class="product-card__description">{{ $product->short_description }}</p>
            @endif
            <a class="text-link" href="{{ route('products.show', $product->slug) }}">View details <span aria-hidden="true">→</span></a>
        @endunless
    </div>
</article>
