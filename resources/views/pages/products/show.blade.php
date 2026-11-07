@extends('layouts.app')
@section('title', $product->seo_title ?: $product->name)
@section('description', $product->seo_description ?: $product->short_description)
@push('head')
    @php
        $productImages = $product->getMedia('featured')->merge($product->getMedia('gallery'))
            ->map(fn ($media) => $product->getMediaUrlIfAvailable($media))
            ->filter()
            ->map(fn (string $url) => ['url' => $url, 'alt' => $product->name])
            ->values();
        $productSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'sku' => $product->sku,
            'description' => $product->seo_description ?: $product->short_description,
            'category' => $product->category?->name,
            'image' => $productImages->pluck('url')->all() ?: null,
            'brand' => [
                '@type' => 'Brand',
                'name' => setting('general.site_name', 'Deshmaya'),
            ],
        ], fn ($value) => $value !== null && $value !== '');
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        $seasonMonths = array_map('intval', $product->season_months ?? []);
        $isInSeason = in_array($currentMonth, $seasonMonths, true);
    @endphp
    <script type="application/ld+json">@json($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
@endpush
@section('content')
<section class="section-shell section-shell--compact product-page">
    <div class="container-shell">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}">Products</a><span aria-hidden="true">/</span>
            @if($product->category)<a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a><span aria-hidden="true">/</span>@endif
            <span aria-current="page">{{ $product->name }}</span>
        </nav>

        <div class="product-showcase">
            <div class="product-detail__visual" @if($productImages->isNotEmpty()) x-data="{ images: @js($productImages), activeImage: @js($productImages->first()['url']) }" @endif>
                @if($productImages->isNotEmpty())
                    <div class="product-detail__main-image-wrap">
                        <img class="product-detail__main-image" :src="activeImage" alt="{{ $product->name }}" fetchpriority="high" decoding="async">
                    </div>
                    @if($productImages->count() > 1)
                        <div class="product-gallery" aria-label="{{ $product->name }} image gallery">
                            @foreach($productImages as $image)
                                <button
                                    class="product-gallery__thumb"
                                    type="button"
                                    aria-label="Show {{ $product->name }} image {{ $loop->iteration }}"
                                    :aria-pressed="activeImage === @js($image['url'])"
                                    @click="activeImage = @js($image['url'])"
                                >
                                    <img src="{{ $image['url'] }}" alt="" loading="lazy" decoding="async">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <x-picture class="product-detail__main-image" :src="'/images/products/'.$product->slug.'.jpg'" :alt="$product->name" :eager="true" />
                @endif
            </div>

            <div class="product-detail__copy">
                @if($product->category)
                    <a class="eyebrow product-detail__category" href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a>
                @endif
                <h1>{{ $product->name }}</h1>
                @if($product->short_description)
                    <p class="product-detail__intro">{{ $product->short_description }}</p>
                @endif
                @if($product->description)
                    <div class="product-detail__description">{{ $product->description }}</div>
                @endif

                @if($product->packaging_options)
                    <section class="packaging-options" aria-labelledby="packaging-options-title">
                        <div class="packaging-options__heading">
                            <h2 id="packaging-options-title">Packaging Options</h2>
                            <span>Dimensions</span>
                        </div>
                        @foreach($product->packaging_options as $option)
                            <div class="packaging-option">
                                <span class="packaging-option__icon" aria-hidden="true">◇</span>
                                <span class="packaging-option__text">
                                    <strong>{{ $option['name'] ?? '' }}</strong>
                                    @if(filled($option['dimensions'] ?? null))
                                        <span>{{ $option['dimensions'] }}</span>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </section>
                @endif

                @if($product->specs)
                    <dl class="spec-list product-detail__specs">
                        @foreach($product->specs as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd></div>
                        @endforeach
                    </dl>
                @endif

                @if($product->sku)
                    <p class="product-detail__sku"><strong>SKU:</strong> {{ $product->sku }}</p>
                @endif
                @if($product->category)
                    <p class="product-detail__category-line">
                        <strong>Category:</strong>
                        <a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a>
                    </p>
                @endif

                @if(filled($product->supplier_guide_title) && filled($product->supplier_guide_url))
                    <a class="supplier-guide" href="{{ $product->supplier_guide_url }}" target="_blank" rel="noopener noreferrer">
                        <span class="supplier-guide__label">Supplier Guide</span>
                        <strong>{{ $product->supplier_guide_title }} <span aria-hidden="true">→</span></strong>
                    </a>
                @endif

                <div class="product-tags" aria-label="Product categories and season">
                    @if($product->category)
                        <a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a>
                    @endif
                    @if($isInSeason)
                        <span>In Season Now</span>
                    @endif
                </div>
                <a class="button button--primary button--wide product-detail__quote" href="{{ route('request-quote', ['product' => $product->slug]) }}">Request a Quote <span aria-hidden="true">→</span></a>
            </div>

            <aside class="in-season-panel" aria-labelledby="in-season-title">
                <div class="in-season-panel__heading">
                    <h2 id="in-season-title">In season now</h2>
                    <span>{{ $months[$currentMonth] }}</span>
                </div>
                @forelse($inSeasonProducts as $seasonProduct)
                    <a class="in-season-product" href="{{ route('products.show', $seasonProduct->slug) }}">
                        @if($seasonImage = $seasonProduct->getAvailableMediaUrl())
                            <img src="{{ $seasonImage }}" alt="" loading="lazy" decoding="async">
                        @else
                            <span class="in-season-product__placeholder" aria-hidden="true">✳</span>
                        @endif
                        <span>{{ $seasonProduct->name }}</span>
                    </a>
                @empty
                    <p class="in-season-panel__empty">No other products are marked in season this month.</p>
                @endforelse
                <a class="in-season-panel__link" href="{{ route('products.index') }}">See all products <span aria-hidden="true">→</span></a>
            </aside>
        </div>
    </div>
</section>

@if($seasonMonths !== [])
    <section class="season-calendar-section" aria-labelledby="season-calendar-title">
        <div class="container-shell">
            <div class="season-calendar__heading">
                <div>
                    <p class="eyebrow">Plan ahead</p>
                    <h2 id="season-calendar-title">Season Calendar</h2>
                    <p>{{ $product->name }}</p>
                </div>
                <div class="season-calendar__legend" aria-label="Season calendar legend">
                    <span><i class="is-available"></i>Fresh / Available</span>
                    <span><i></i>Out of season</span>
                </div>
            </div>
            <ol class="season-months">
                @foreach($months as $monthNumber => $monthName)
                    @php($available = in_array($monthNumber, $seasonMonths, true))
                    <li @class(['season-month', 'is-available' => $available, 'is-current' => $monthNumber === $currentMonth])>
                        <span class="season-month__name">{{ $monthName }}</span>
                        <span class="season-month__status">{{ $available ? 'Fresh' : 'Unavailable' }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif

@if($relatedProducts->isNotEmpty())
    <section class="section-shell related-products-section">
        <div class="container-shell">
            <div class="related-products-heading">
                <div>
                    <p class="eyebrow">From the same category</p>
                    <h2>More {{ $product->category?->name ?? 'Fresh Produce' }}</h2>
                </div>
                @if($product->category)
                    <a class="text-link" href="{{ route('products.category', $product->category->slug) }}">See the whole category <span aria-hidden="true">→</span></a>
                @endif
            </div>
            <div class="related-product-grid">
                @foreach($relatedProducts as $relatedProduct)
                    <a class="related-product" href="{{ route('products.show', $relatedProduct->slug) }}">
                        @if($relatedImage = $relatedProduct->getAvailableMediaUrl())
                            <img src="{{ $relatedImage }}" alt="{{ $relatedProduct->name }}" loading="lazy" decoding="async">
                        @else
                            <x-picture :src="'/images/products/'.$relatedProduct->slug.'.jpg'" :alt="$relatedProduct->name" />
                        @endif
                        <span>{{ $relatedProduct->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
