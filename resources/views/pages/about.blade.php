@extends('layouts.app')
@section('title', 'About Us')
@section('description', 'Learn about Deshmaya, a Bangladesh-based agricultural exporter connecting local growers with global markets.')
@section('content')
<x-page-banner title="About Us" subtitle="Our story, our commitment, our vision." tone="land" />

<section class="section-shell">
    <div class="container-shell split-feature split-feature--about">
        <div class="split-feature__copy">
            <p class="eyebrow">Who we are</p>
            <h2>Growing Bangladesh’s Goodness for the World</h2>
            <p>Deshmaya is a Bangladesh-based agricultural export company focused on supplying high-quality fruits, vegetables and agricultural products to international buyers.</p>
            <p>We work with trusted farmers and suppliers, carefully coordinating selection, preparation and delivery to meet the needs of every order.</p>
            <a class="text-link" href="{{ route('products.index') }}">Explore our products <span aria-hidden="true">→</span></a>
        </div>
        <x-image-placeholder class="split-feature__image" label="Farmer selecting fresh produce photography pending" tone="land" />
    </div>
</section>

<section class="section-shell section-shell--tint">
    <div class="container-shell">
        <x-section-heading eyebrow="Our foundation" title="Our Mission & Vision" center />
        <div class="value-grid value-grid--two">
            <article class="value-card" data-reveal>
                <span class="value-card__icon" aria-hidden="true">◎</span>
                <h3>Our Mission</h3>
                <p>To connect Bangladesh’s agricultural excellence with global markets through responsible sourcing, dependable quality and reliable export service.</p>
            </article>
            <article class="value-card" data-reveal>
                <span class="value-card__icon" aria-hidden="true">↗</span>
                <h3>Our Vision</h3>
                <p>To become a trusted global partner, known for the freshness, care and consistency we bring to every shipment.</p>
            </article>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="container-shell">
        <x-section-heading eyebrow="What guides us" title="Our Values" description="The principles behind the way we source, prepare and deliver." center />
        <div class="value-grid">
            @foreach([
                ['icon' => '✳', 'title' => 'Quality Products', 'text' => 'Thoughtful selection and careful handling.'],
                ['icon' => '⇄', 'title' => 'Trusted Partners', 'text' => 'Strong relationships built on clear communication.'],
                ['icon' => '◎', 'title' => 'Global Reach', 'text' => 'A responsive approach to international markets.'],
                ['icon' => '♧', 'title' => 'Sustainable Future', 'text' => 'Respect for the people and resources behind every harvest.'],
            ] as $value)
                <article class="value-card" data-reveal>
                    <span class="value-card__icon" aria-hidden="true">{{ $value['icon'] }}</span>
                    <h3>{{ $value['title'] }}</h3>
                    <p>{{ $value['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
