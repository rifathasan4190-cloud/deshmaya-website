@props([
    'title',
    'subtitle' => null,
    'tone' => 'produce',
    'showBreadcrumb' => true,
])

<section class="page-banner">
    <x-image-placeholder class="page-banner__image" :label="$title.' banner photo pending'" :tone="$tone" />
    <div class="page-banner__shade"></div>
    <span class="photo-placeholder-note">Photo pending</span>
    <div class="page-banner__content container-shell">
        @if($showBreadcrumb)
            <nav class="page-banner__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $title }}</span>
            </nav>
        @endif
        <p class="eyebrow text-white/85">Deshmaya Global Agri Exporter</p>
        <h1>{{ $title }}</h1>
        @if($subtitle)<p class="page-banner__subtitle">{{ $subtitle }}</p>@endif
    </div>
</section>
