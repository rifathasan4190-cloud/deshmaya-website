@extends('layouts.app')
@section('title', 'Certifications & Documentation')
@section('description', 'Learn about the quality checks and export documentation coordinated for Deshmaya shipments.')
@section('content')
<x-page-banner title="Certifications" subtitle="Quality you can verify." tone="quality" image="/images/banners/certifications.jpg" />

<section class="section-shell">
    <div class="container-shell">
        <x-section-heading eyebrow="Prepared for your destination" title="Documentation for every shipment" description="Documentation and certifications vary by product, buyer and destination. Requirements are confirmed with you before shipment." center />
        <div class="value-grid">
            @foreach([
                ['icon' => '✳', 'title' => 'Phytosanitary', 'text' => 'Plant health documentation arranged where required for the destination market.'],
                ['icon' => '▤', 'title' => 'Certificate of Origin', 'text' => 'Origin documentation coordinated with the relevant shipment paperwork.'],
                ['icon' => '✓', 'title' => 'Quality Records', 'text' => 'Product checks and shipment details prepared to meet agreed specifications.'],
                ['icon' => '◎', 'title' => 'Destination Standards', 'text' => 'Buyer and market requirements reviewed during order preparation.'],
            ] as $item)
                <article class="value-card value-card--certificate" data-reveal>
                    <span class="value-card__icon" aria-hidden="true">{{ $item['icon'] }}</span>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </article>
            @endforeach
        </div>
        <p class="notice-copy">Specific certifications are confirmed per product and shipment. Please contact our export team for current documentation and market requirements.</p>
    </div>
</section>

<section class="cta-band">
    <div class="container-shell cta-band__inner">
        <div><p class="eyebrow text-white/75">Need a specific document?</p><h2>Let’s discuss your destination requirements.</h2></div>
        <a class="button button--light" href="{{ route('contact') }}">Contact Us <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
