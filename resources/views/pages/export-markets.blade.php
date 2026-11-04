@extends('layouts.app')
@section('title', 'Export Markets')
@section('description', 'Deshmaya connects Bangladesh-sourced agricultural products with buyers across international markets.')
@section('content')
<x-page-banner title="Export Markets" subtitle="From Bangladesh to global markets." tone="markets" />

<section class="section-shell">
    <div class="container-shell market-layout">
        <div class="market-copy">
            <p class="eyebrow">Across borders</p>
            <h2>Our Market Reach</h2>
            <p>We work with importers and buyers to coordinate agricultural shipments from Bangladesh to destinations across the region and beyond.</p>
            <p>Each inquiry is reviewed individually so product availability, documentation and logistics can be aligned with the destination.</p>
            <a class="button button--primary" href="{{ route('request-quote') }}">Discuss Your Market <span aria-hidden="true">→</span></a>
        </div>
        <div class="market-map" role="img" aria-label="Illustrative map graphic pending approved artwork">
            <span class="market-map__tag">Map artwork pending</span>
            <span class="market-map__origin">Bangladesh</span>
            @foreach([
                ['country' => 'Malaysia', 'position' => 'market-pin--malaysia'],
                ['country' => 'UAE', 'position' => 'market-pin--uae'],
                ['country' => 'Saudi Arabia', 'position' => 'market-pin--saudi'],
                ['country' => 'Qatar', 'position' => 'market-pin--qatar'],
                ['country' => 'Oman', 'position' => 'market-pin--oman'],
                ['country' => 'Kuwait', 'position' => 'market-pin--kuwait'],
            ] as $market)
                <span class="market-pin {{ $market['position'] }}" aria-hidden="true"></span>
            @endforeach
        </div>
    </div>
</section>

<section class="section-shell section-shell--tint">
    <div class="container-shell">
        <x-section-heading eyebrow="Markets of focus" title="Expanding across global markets" description="We welcome inquiries from buyers in established and emerging destinations." center />
        <div class="market-list">
            @foreach(['Malaysia', 'United Arab Emirates', 'Saudi Arabia', 'Qatar', 'Oman', 'Kuwait'] as $market)
                <div class="market-list__item" data-reveal><span class="market-list__mark" aria-hidden="true">◎</span><strong>{{ $market }}</strong></div>
            @endforeach
        </div>
    </div>
</section>
@endsection
