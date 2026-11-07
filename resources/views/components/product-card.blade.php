@props(['product'])

@php
    $image = $product->getAvailableMediaUrl();
@endphp

<article class="product-card hover-lift" data-reveal>
    <a class="product-card__image-link" href="{{ route('products.show', $product->slug) }}" aria-label="View {{ $product->name }}">
        @if($image)
            <img class="product-card__image" src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
        @else
            <x-picture class="product-card__image" :src="'/images/products/'.$product->slug.'.jpg'" :alt="$product->name" />
        @endif
    </a>
    <div class="product-card__body">
        <p class="product-card__category">{{ $product->category?->name ?? 'Fresh produce' }}</p>
        <h3><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h3>
        @if($product->short_description)
            <p class="product-card__description">{{ $product->short_description }}</p>
        @endif
        <a class="text-link" href="{{ route('products.show', $product->slug) }}">View details <span aria-hidden="true">→</span></a>
    </div>
</article>
