@extends('layouts.app')
@section('title', 'How We Export')
@section('description', 'A transparent export process, from product sourcing and quality checks to packing and delivery.')
@section('content')
<x-page-banner title="How We Export" subtitle="From farm to global market." tone="shipping" />

<section class="section-shell">
    <div class="container-shell">
        <x-section-heading eyebrow="A clear journey" title="From farm to global market" description="Every shipment follows a coordinated process designed around product quality, buyer specifications and destination requirements." center />
        <ol class="process-grid">
            @foreach([
                ['number' => '01', 'icon' => '⌂', 'title' => 'Sourcing', 'text' => 'We coordinate with growers and trusted suppliers.'],
                ['number' => '02', 'icon' => '◎', 'title' => 'Quality Inspection', 'text' => 'Produce is checked against agreed order requirements.'],
                ['number' => '03', 'icon' => '↕', 'title' => 'Sorting & Grading', 'text' => 'Products are selected and prepared to buyer specifications.'],
                ['number' => '04', 'icon' => '▣', 'title' => 'Packaging', 'text' => 'Packing is arranged to suit the product and shipment.'],
                ['number' => '05', 'icon' => '▤', 'title' => 'Documentation', 'text' => 'Required export documents are coordinated for the order.'],
                ['number' => '06', 'icon' => '▱', 'title' => 'Shipment', 'text' => 'Transport and dispatch are arranged with clear updates.'],
                ['number' => '07', 'icon' => '↗', 'title' => 'Delivery', 'text' => 'Your shipment travels to its agreed destination.'],
            ] as $step)
                <li class="process-card" data-reveal>
                    <span class="process-card__number">{{ $step['number'] }}</span>
                    <span class="process-card__icon" aria-hidden="true">{{ $step['icon'] }}</span>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

<section class="export-promise">
    <x-image-placeholder class="export-promise__image" label="Bangladesh export shipment photography pending" tone="shipping" />
    <div class="export-promise__shade"></div>
    <span class="photo-placeholder-note">Photo pending</span>
    <div class="container-shell export-promise__content">
        <p class="eyebrow text-white/80">Your trust, our priority</p>
        <h2>Every shipment prepared with care.</h2>
        <p>From the first product discussion to final dispatch, our team keeps your requirements at the center of the process.</p>
        <a class="button button--light" href="{{ route('request-quote') }}">Start an Inquiry <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
