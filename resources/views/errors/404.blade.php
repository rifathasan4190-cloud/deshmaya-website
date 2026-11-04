@extends('layouts.app')
@section('title', 'Page not found')
@section('content')
    <section class="section-shell">
        <div class="container-shell">
            <x-section-heading eyebrow="404 error" title="Page not found" description="The page may have moved, or the link may be incorrect." :level="1" />
            <div class="text-center">
                <a class="button button--primary" href="{{ route('home') }}">Return to home</a>
            </div>
        </div>
    </section>
@endsection
