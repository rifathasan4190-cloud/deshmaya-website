@extends('layouts.app')
@section('title', $product->seo_title ?: $product->name)
@section('description', $product->seo_description ?: $product->short_description)
@push('head')
    @php
        $productImages = $product->getMedia('featured')->merge($product->getMedia('gallery'))
            ->map(fn ($media) => $media->getUrl('preview') ?: $media->getUrl())
            ->values()
            ->all();
        $productSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->seo_description ?: $product->short_description,
            'category' => $product->category?->name,
            'image' => $productImages ?: null,
            'brand' => [
                '@type' => 'Brand',
                'name' => setting('general.site_name', 'Deshmaya'),
            ],
        ], fn ($value) => $value !== null && $value !== '');
    @endphp
    <script type="application/ld+json">@json($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
@endpush
@section('content')
<x-page-banner :title="$product->name" :subtitle="$product->short_description" tone="produce" :show-breadcrumb="false" />

<section class="section-shell section-shell--compact">
    <div class="container-shell">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}">Products</a><span aria-hidden="true">/</span>
            @if($product->category)<a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a><span aria-hidden="true">/</span>@endif
            <span aria-current="page">{{ $product->name }}</span>
        </nav>
        <div class="product-detail">
            @php($featuredImage = $product->getFirstMediaUrl('featured', 'preview') ?: $product->getFirstMediaUrl('featured'))
            <div class="product-detail__visual">
                @if($featuredImage)
                    <img class="product-detail__main-image" src="{{ $featuredImage }}" alt="{{ $product->name }}" loading="eager" decoding="async" fetchpriority="high">
                @else
                    <x-image-placeholder class="product-detail__main-image" :label="$product->name.' photo pending'" />
                @endif
                @if($product->getMedia('gallery')->isNotEmpty())
                    <div class="product-gallery" aria-label="{{ $product->name }} gallery">
                        @foreach($product->getMedia('gallery') as $image)
                            <img src="{{ $image->getUrl('preview') }}" alt="{{ $product->name }} gallery image {{ $loop->iteration }}" loading="lazy">
                        @endforeach
                    </div>
                @else
                    <div class="product-gallery product-gallery--placeholders" aria-label="Additional product photos pending">
                        @foreach(['Harvest', 'Freshness', 'Selection', 'Packing', 'Details'] as $label)
                            <x-image-placeholder :label="$product->name.' '.$label.' photo pending'" />
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="product-detail__copy">
                <p class="eyebrow">{{ $product->category?->name ?? 'Fresh produce' }}</p>
                <h2>{{ $product->name }}</h2>
                @if($product->short_description)<p class="product-detail__intro">{{ $product->short_description }}</p>@endif
                @if($product->description)<p class="prose-copy">{{ $product->description }}</p>@endif

                @if($product->specs)
                    <dl class="spec-list">
                        @foreach($product->specs as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd></div>
                        @endforeach
                    </dl>
                @endif

                <a class="button button--primary button--wide" href="{{ route('request-quote', ['product' => $product->slug]) }}">Request a Quote <span aria-hidden="true">→</span></a>
                <p class="product-detail__note">Availability, season and specifications are confirmed for each order.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-shell section-shell--tint">
    <div class="container-shell">
        <x-section-heading eyebrow="Care at every step" title="Product Highlights" description="Prepared to meet buyer requirements and protect freshness in transit." />
        <div class="highlight-list">
            @foreach(['Selected for freshness', 'Quality checks during preparation', 'Buyer-specific grading and packing', 'Export documentation coordinated per order'] as $highlight)
                <p><span aria-hidden="true">✓</span>{{ $highlight }}</p>
            @endforeach
        </div>
    </div>
</section>

@if($relatedProducts->isNotEmpty())
    <section class="section-shell">
        <div class="container-shell">
            <x-section-heading eyebrow="You may also like" title="Related Products" />
            <div class="product-grid product-grid--related">
                @foreach($relatedProducts as $relatedProduct)
                    <x-product-card :product="$relatedProduct" />
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
