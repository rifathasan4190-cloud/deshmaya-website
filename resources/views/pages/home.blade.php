@extends('layouts.app')
@section('title', 'Fresh Produce from Bangladesh')
@section('description', 'Premium fruits, vegetables and agricultural products, responsibly sourced in Bangladesh and delivered to global markets.')
@section('content')
<section class="home-hero">
    <x-image-placeholder class="home-hero__image" label="Bangladesh produce field and harvest photography pending" tone="hero" />
    <div class="home-hero__shade"></div>
    <span class="photo-placeholder-note">Photo pending</span>
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

<section class="section-shell" id="categories">
    <div class="container-shell">
        <x-section-heading eyebrow="From farm to market" title="What are you looking for?" description="Select a category to explore our growing range of export-ready products." center />
        <div class="category-grid">
            @forelse($categories as $category)
                @php($categoryImage = $category->getFirstMediaUrl('image', 'preview') ?: $category->getFirstMediaUrl('image'))
                <a class="category-card hover-lift" href="{{ route('products.category', $category->slug) }}">
                    @if($categoryImage)
                        <img src="{{ $categoryImage }}" alt="" class="category-card__image" loading="lazy" decoding="async">
                    @else
                        <x-image-placeholder class="category-card__image" :label="$category->name.' category photo pending'" />
                    @endif
                    <span class="category-card__overlay"></span>
                    <span class="category-card__content">
                        <strong>{{ $category->name }}</strong>
                        <span>Explore {{ $category->name }} <span aria-hidden="true">→</span></span>
                    </span>
                </a>
            @empty
                @foreach(['Fruits', 'Vegetables', 'Agricultural Products'] as $name)
                    <a class="category-card hover-lift" href="{{ route('products.index') }}">
                        <x-image-placeholder class="category-card__image" :label="$name.' category photo pending'" />
                        <span class="category-card__overlay"></span>
                        <span class="category-card__content"><strong>{{ $name }}</strong><span>Explore products <span aria-hidden="true">→</span></span></span>
                    </a>
                @endforeach
            @endforelse
        </div>
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
        <x-image-placeholder class="split-feature__image" label="Bangladesh farm and grower photography pending" tone="land" />
    </div>
</section>

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

<section class="benefits-band">
    <div class="container-shell">
        <x-section-heading eyebrow="Why choose Deshmaya?" title="A trusted partner for every shipment" description="From sourcing to delivery, we work to make every step clear, dependable and quality focused." center />
        <div class="benefit-grid">
            @foreach([
                ['icon' => '⌂', 'title' => 'Direct Sourcing', 'text' => 'Working closely with growers and trusted suppliers.'],
                ['icon' => '✓', 'title' => 'Quality Control', 'text' => 'Careful checks at every stage of preparation.'],
                ['icon' => '▣', 'title' => 'Export-ready Packaging', 'text' => 'Packed to align with product and buyer requirements.'],
                ['icon' => '♧', 'title' => 'Reliable Supply', 'text' => 'Clear communication from order to shipment.'],
                ['icon' => '◎', 'title' => 'Global Logistics', 'text' => 'Coordinated handling for international delivery.'],
            ] as $benefit)
                <article class="benefit-card" data-reveal>
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
        <x-image-placeholder class="split-feature__image" label="Export-ready produce shipment photography pending" tone="shipping" />
    </div>
</section>
@endsection
