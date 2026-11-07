@extends('layouts.app')
@section('title', 'Quality & Packaging')
@section('description', 'Learn how Deshmaya coordinates careful handling and packaging for export-ready agricultural products.')
@section('content')
<x-page-banner title="Quality & Packaging" subtitle="Careful handling, from harvest to shipment." tone="quality" image="/images/banners/quality-packaging.jpg" />

<section class="section-shell">
    <div class="container-shell">
        <x-section-heading eyebrow="Quality at every step" title="Freshness, protected with care" description="We coordinate careful handling and buyer-specific preparation from product selection through final packing." center />
        <div class="quality-grid">
            @foreach([
                ['title' => 'Sorting', 'text' => 'Produce is selected and sorted to match the agreed order requirements.', 'image' => '/images/quality/sorting.jpg'],
                ['title' => 'Grading', 'text' => 'Size and grade are aligned with the buyer’s product specifications.', 'image' => '/images/quality/grading.jpg'],
                ['title' => 'Cleaning', 'text' => 'Preparation and handling are arranged with product care in mind.', 'image' => '/images/quality/cleaning.jpg'],
                ['title' => 'Packing', 'text' => 'Packaging formats are discussed to support the product and journey.', 'image' => '/images/quality/packing.jpg'],
            ] as $stage)
                <article class="quality-card hover-lift" data-reveal>
                    <x-picture class="quality-card__image" :src="$stage['image']" :alt="$stage['title'].' export produce'" />
                    <div class="quality-card__body"><h3>{{ $stage['title'] }}</h3><p>{{ $stage['text'] }}</p></div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section-shell section-shell--tint">
    <div class="container-shell packaging-layout">
        <div>
            <p class="eyebrow">Packaging solutions</p>
            <h2>Prepared around your product.</h2>
            <p class="prose-copy">Packaging depends on the product, destination and buyer requirements. Our team confirms suitable options and shipment details with you before an order is finalized.</p>
        </div>
        <ul class="check-list">
            <li>Product-appropriate packing formats</li>
            <li>Buyer and destination requirements reviewed</li>
            <li>Clear shipment and labeling coordination</li>
            <li>Export documentation discussed per order</li>
        </ul>
    </div>
</section>

<section class="cta-band">
    <div class="container-shell cta-band__inner">
        <div><p class="eyebrow text-white/75">Have a specification in mind?</p><h2>Let’s find the right solution.</h2></div>
        <a class="button button--light" href="{{ route('contact') }}">Talk to our team <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
