@extends('layouts.app')
@section('title', 'Our Products')
@section('description', 'Explore fresh fruits, vegetables and agricultural products sourced in Bangladesh for export.')
@section('content')
<x-page-banner title="Our Products" subtitle="Fresh produce, thoughtfully selected for global markets." tone="produce" />

<section class="section-shell">
    <div class="container-shell">
        <x-section-heading eyebrow="From our sourcing network" title="Fresh from Bangladesh" description="Browse our current selection of export-ready agricultural products." center />
        <nav class="category-pills" aria-label="Filter products by category">
            <a @class(['category-pill', 'is-active' => ! $selectedCategory]) href="{{ route('products.index') }}" @if(! $selectedCategory) aria-current="page" @endif>All Products</a>
            @foreach($categories as $category)
                <a @class(['category-pill', 'is-active' => $selectedCategory === $category->slug]) href="{{ route('products.category', $category->slug) }}" @if($selectedCategory === $category->slug) aria-current="page" @endif>{{ $category->name }}</a>
            @endforeach
        </nav>

        @if($products->isNotEmpty())
            <div class="product-grid">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <div class="empty-state empty-state--large">
                <span class="empty-state__icon" aria-hidden="true">✳</span>
                <h2>{{ $selectedCategory ? 'More products coming soon' : 'Our collection is being prepared' }}</h2>
                <p>Please contact our team to discuss your sourcing requirements.</p>
                <a class="button button--primary" href="{{ route('contact') }}">Talk to our team</a>
            </div>
        @endif
    </div>
</section>

<section class="cta-band">
    <div class="container-shell cta-band__inner">
        <div>
            <p class="eyebrow text-white/75">Sourcing something specific?</p>
            <h2>Tell us what your market needs.</h2>
        </div>
        <a class="button button--light" href="{{ route('request-quote') }}">Request a Quote <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
