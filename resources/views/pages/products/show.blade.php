@extends('layouts.app')
@section('title', $product->seo_title ?: $product->name)
@section('description', $product->seo_description ?: $product->short_description ?: "Fresh {$product->name} prepared for export buyers.")
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
        $usesDemoSku = blank($product->sku);
        $usesDemoPackaging = blank($product->packaging_options);
        $usesDemoSeason = blank($product->season_months);
        $usesDemoGuide = blank($product->supplier_guide_title) || blank($product->supplier_guide_url);
        $usesDemoContent = $usesDemoSku || $usesDemoPackaging || $usesDemoSeason || $usesDemoGuide
            || blank($product->short_description) || blank($product->description);
        $displaySku = $product->sku ?: 'DEMO-'.strtoupper(str_replace('-', '', $product->slug));
        $packagingOptions = $product->packaging_options ?: [
            ['name' => 'Carton Box (Sample)', 'dimensions' => '60 x 40 x 15 cm (sample)'],
        ];
        $seasonMonths = filled($product->season_months)
            ? array_map('intval', $product->season_months)
            : $demoSeasonMonths;
        $isInSeason = in_array($currentMonth, $seasonMonths, true);
        $categoryTags = collect([$product->category?->parent, $product->category])
            ->filter()
            ->unique('id')
            ->values();
        $socialNetworks = collect(['facebook', 'instagram', 'x', 'linkedin'])
            ->map(fn (string $network) => ['name' => $network, 'url' => setting("social.$network")])
            ->filter(fn (array $network) => filled($network['url']));
    @endphp
    <script type="application/ld+json">@json($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
@endpush
@section('content')
<section class="section-shell section-shell--compact product-page">
    <div class="container-shell">
        <div class="product-showcase">
            <div class="product-detail__visual" @if($productImages->isNotEmpty()) x-data="{ images: @js($productImages), activeImage: @js($productImages->first()['url']) }" @endif>
                @if($productImages->isNotEmpty())
                    <div class="product-detail__main-image-wrap">
                        <img class="product-detail__main-image" :src="activeImage" alt="{{ $product->name }}" fetchpriority="high" decoding="async">
                        <a class="product-detail__zoom" :href="activeImage" target="_blank" rel="noopener noreferrer" aria-label="Open full-size {{ $product->name }} image">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5M10.8 7.5v6.6M7.5 10.8h6.6"></path></svg>
                        </a>
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
                    <div class="product-detail__main-image-wrap">
                        <x-picture class="product-detail__main-image" :src="'/images/products/'.$product->slug.'.jpg'" :alt="$product->name" :eager="true" />
                    </div>
                @endif
            </div>

            <div class="product-detail__copy">
                <nav class="breadcrumbs product-detail__breadcrumbs" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
                    @if($product->category)
                        <a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a><span aria-hidden="true">/</span>
                    @endif
                    <span aria-current="page">{{ $product->name }}</span>
                </nav>
                @if($usesDemoContent)
                    <p class="product-detail__demo-note">Demo content — confirm these sample details in Product admin before publishing.</p>
                @endif
                <h1>{{ $product->name }}</h1>
                <div class="product-detail__description">
                    @if(filled($product->short_description))
                        <p>{{ $product->short_description }}</p>
                    @else
                        <p>Sample description for {{ $product->name }}, prepared as an example for your product listing.</p>
                    @endif
                    @if(filled($product->description))
                        <p>{{ $product->description }}</p>
                    @else
                        <p>Products are carefully handled and prepared to buyer requirements. Confirm grading, harvest timing and availability for each order.</p>
                    @endif
                    @if($usesDemoContent)
                        <p>Sample export packing and availability are shown for layout preview only. Please replace with confirmed product information.</p>
                    @endif
                </div>

                <section class="packaging-options" aria-labelledby="packaging-options-title">
                    <div class="packaging-options__heading">
                        <h2 id="packaging-options-title">Packaging Options</h2>
                        <span>{{ $usesDemoPackaging ? 'Sample dimensions (cm)' : 'Dimensions (cm)' }}</span>
                    </div>
                    @foreach($packagingOptions as $option)
                        <div class="packaging-option">
                            <span class="packaging-option__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M4 8.5 12 4l8 4.5v8L12 21l-8-4.5zM4 8.5l8 4.5m8-4.5-8 4.5m0 0V21m-4-9.5 8-4.5"></path></svg>
                            </span>
                            <span class="packaging-option__text">
                                <strong>{{ $option['name'] ?? 'Sample package' }}</strong>
                                @if(filled($option['dimensions'] ?? null))
                                    <span>{{ $option['dimensions'] }}</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </section>

                <p class="product-detail__sku"><strong>{{ $usesDemoSku ? 'Demo SKU:' : 'SKU:' }}</strong> {{ $displaySku }}</p>
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
                @else
                    <div class="supplier-guide supplier-guide--demo">
                        <span class="supplier-guide__label">Supplier Guide (Sample)</span>
                        <strong>{{ $product->name }} Supplier Guide</strong>
                        <span class="supplier-guide__note">Add a real guide link in Product admin.</span>
                    </div>
                @endif

                <div class="product-tags" aria-label="Product categories and season">
                    @foreach($categoryTags as $tag)
                        @if($tag->parent_id)
                            <a href="{{ route('products.category', $tag->slug) }}">{{ $tag->name }}</a>
                        @else
                            <span>{{ $tag->name }}</span>
                        @endif
                    @endforeach
                    @if($isInSeason)
                        <span>In Season Now{{ $usesDemoSeason ? ' (Sample)' : '' }}</span>
                    @endif
                </div>

                @if($socialNetworks->isNotEmpty())
                    <div class="product-follow">
                        <span>Follow us</span>
                        <div class="product-follow__links">
                            @foreach($socialNetworks as $network)
                                <a href="{{ $network['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network['name']) }}">
                                    {{ strtoupper(substr($network['name'], 0, 1)) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <a class="product-detail__quote-link" href="{{ route('request-quote', ['product' => $product->slug]) }}">Request a Quote <span aria-hidden="true">→</span></a>
            </div>

            <aside class="in-season-panel" aria-labelledby="in-season-title">
                <div class="in-season-panel__heading">
                    <h2 id="in-season-title">In season now</h2>
                    <span>{{ $months[$currentMonth] }}</span>
                </div>
                @if($usesDemoSeason)
                    <p class="in-season-panel__demo-note">Sample availability</p>
                @endif
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
                <a class="in-season-panel__link" href="{{ route('products.index') }}">See everything available this month <span aria-hidden="true">→</span></a>
            </aside>
        </div>
    </div>
</section>

<section class="season-calendar-section" aria-labelledby="season-calendar-title">
    <div class="container-shell">
        <div class="season-calendar__heading">
            <div>
                <p class="eyebrow">Season Calendar</p>
                <h2 id="season-calendar-title">{{ $product->name }}</h2>
            </div>
            <div class="season-calendar__legend" aria-label="Season calendar legend">
                <span><i class="is-available"></i>{{ $usesDemoSeason ? 'Sample / confirm availability' : 'Fresh / Available' }}</span>
                <span><i></i>Out of season</span>
            </div>
        </div>
        <ol class="season-months">
            @foreach($months as $monthNumber => $monthName)
                @php($available = in_array($monthNumber, $seasonMonths, true))
                <li class="season-month{{ $available ? ' is-available' : '' }}{{ $monthNumber === $currentMonth ? ' is-current' : '' }}" aria-label="{{ $monthName }}: {{ $available ? 'fresh' : 'out of season' }}">
                    <span class="season-month__name">{{ substr($monthName, 0, 3) }}</span>
                    <span class="season-month__dot" aria-hidden="true"></span>
                    <span class="season-month__status">{{ $available ? 'Fresh' : '—' }}</span>
                </li>
            @endforeach
        </ol>
    </div>
</section>

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
