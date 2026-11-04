@extends('layouts.app')
@section('title', 'Something went wrong')
@section('content')
    <section class="section-shell">
        <div class="container-shell">
            <x-section-heading eyebrow="Server error" title="Something went wrong" description="Please try again in a few moments." :level="1" />
            <div class="text-center">
                <a class="button button--primary" href="{{ route('home') }}">Return to home</a>
            </div>
        </div>
    </section>
@endsection
