@extends('layouts.app')
@section('title', 'Fresh Produce from Bangladesh')
@section('description', 'Premium fruits, vegetables and agricultural products, responsibly sourced in Bangladesh and delivered to global markets.')
@section('content')
<section class="section-shell" id="categories">
    <div class="container-shell">
        <x-section-heading eyebrow="From farm to market" title="Explore Our Produce" description="Browse fresh, export-ready products from our growing selection." center />
        @if($carouselProducts->isNotEmpty())
            <div class="produce-marquee" aria-label="Browse our produce">
                <div class="produce-marquee__controls">
                    <button class="produce-marquee__toggle" type="button" data-produce-marquee-toggle aria-label="Pause automatic produce scrolling" aria-pressed="false">Pause</button>
                </div>
                <div class="produce-marquee__viewport">
                    <div class="produce-marquee__track" style="--produce-marquee-duration: {{ max(22, $carouselProducts->count() * 4) }}s">
                        @for($copy = 0; $copy < 2; $copy++)
                            <div class="produce-marquee__group" @if($copy) aria-hidden="true" inert @endif>
                                @foreach($carouselProducts as $product)
                                    @php($productImage = $product->getAvailableMediaUrl())
                                    <a class="produce-marquee__item" href="{{ route('products.show', $product->slug) }}">
                                        <span class="produce-marquee__media">
                                            @if($productImage)
                                                <img class="produce-marquee__image" src="{{ $productImage }}" alt="{{ $product->name }}" loading="lazy" decoding="async" data-produce-image>
                                            @else
                                                <x-picture class="produce-marquee__image" :src="'/images/products/'.$product->slug.'.jpg'" :alt="$product->name" data-produce-image />
                                            @endif
                                        </span>
                                        <span class="produce-marquee__name">{{ $product->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @else
            <p class="empty-state">Our product collection is being prepared. Please check back soon.</p>
        @endif
    </div>
</section>

<section class="home-hero">
    <x-picture class="home-hero__image" src="/images/homepage-banner.jpg" alt="" :eager="true" />
    <div class="home-hero__shade"></div>
    <div class="home-hero__content container-shell">
        <p class="eyebrow text-white/85" data-reveal>Fresh from Bangladesh.</p>
        <h1 data-reveal style="--reveal-delay:80ms">Delivered to the <span>World.</span></h1>
        <p class="home-hero__copy" data-reveal style="--reveal-delay:140ms">Premium fruits, vegetables and agricultural products, thoughtfully sourced for international markets.</p>
        <div class="button-row" data-reveal style="--reveal-delay:200ms">
            <a class="button button--primary" href="{{ route('products.index') }}">Explore Products <span aria-hidden="true">→</span></a>
            <a class="button button--light" href="{{ route('request-quote') }}">Request a Quote</a>
        </div>
    </div>
</section>

<section class="trust-strip" aria-label="Our commitments">
    <div class="container-shell trust-strip__grid">
        @foreach([
            ['icon' => '✓', 'title' => 'Quality Assured', 'text' => 'Careful selection'],
            ['icon' => '◇', 'title' => 'Export Grade', 'text' => 'Buyer-ready produce'],
            ['icon' => '↗', 'title' => 'Reliable Supply', 'text' => 'Consistent coordination'],
            ['icon' => '◎', 'title' => 'Global Delivery', 'text' => 'International logistics'],
        ] as $item)
            <div class="trust-item">
                <span class="trust-item__icon" aria-hidden="true">{{ $item['icon'] }}</span>
                <span><strong>{{ $item['title'] }}</strong><small>{{ $item['text'] }}</small></span>
            </div>
        @endforeach
    </div>
</section>

<section class="section-shell section-shell--tint">
    <div class="container-shell split-feature">
        <div class="split-feature__copy">
            <p class="eyebrow">Rooted in Bangladesh</p>
            <h2>Connecting Bangladesh’s Agriculture with the World</h2>
            <p>Deshmaya is a Bangladesh-based agricultural export company focused on supplying high-quality fruits, vegetables and agricultural products to international buyers. We work with trusted farmers and suppliers to ensure freshness, quality and timely delivery.</p>
            <a class="button button--primary" href="{{ route('about') }}">Read More <span aria-hidden="true">→</span></a>
        </div>
        <x-picture class="split-feature__image" src="/images/features/home-farm.jpg" alt="Bangladesh farm and growers" sizes="(min-width: 1024px) 50vw, 100vw" />
    </div>
</section>

{{--
<section class="section-shell">
    <div class="container-shell">
        <div class="section-heading section-heading--row">
            <div>
                <p class="eyebrow">Selected with care</p>
                <h2>Our Products</h2>
                <p class="section-heading__description">Freshness you can trust, prepared for international markets.</p>
            </div>
            <a class="text-link section-heading__action" href="{{ route('products.index') }}">View all products <span aria-hidden="true">→</span></a>
        </div>
        <div class="product-grid">
            @forelse($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <p class="empty-state">Our product collection is being prepared. Please check back soon.</p>
            @endforelse
        </div>
    </div>
</section>
--}}

<section class="benefits-band">
    <div class="container-shell">
        <h2 class="benefits-band__title">From the field to shipping</h2>
        <div class="benefit-grid">
            @foreach([
                ['icon' => '♧', 'title' => 'Harvest', 'text' => 'Picked at the right stage'],
                ['icon' => '✓', 'title' => 'Quality check', 'text' => 'Checked against order requirements'],
                ['icon' => '▣', 'title' => 'Packaging', 'text' => 'Prepared to buyer specifications'],
                ['icon' => '❄', 'title' => 'Cold chain', 'text' => 'Temperature managed to protect freshness'],
                ['icon' => '↗', 'title' => 'Shipping', 'text' => 'Coordinated from order to delivery'],
            ] as $benefit)
                <article class="benefit-card">
                    <span class="benefit-card__icon" aria-hidden="true">{{ $benefit['icon'] }}</span>
                    <h3>{{ $benefit['title'] }}</h3>
                    <p>{{ $benefit['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="container-shell split-feature split-feature--reverse">
        <div class="split-feature__copy">
            <p class="eyebrow">A clear, careful journey</p>
            <h2>Our Export Process</h2>
            <p>Every order is coordinated from product selection and quality checks through packing, documentation and delivery.</p>
            <a class="button button--primary" href="{{ route('how-we-export') }}">Learn More <span aria-hidden="true">→</span></a>
        </div>
        <x-picture class="split-feature__image" src="/images/features/home-shipment.jpg" alt="Export-ready produce shipment" sizes="(min-width: 1024px) 50vw, 100vw" />
    </div>
</section>
@endsection
