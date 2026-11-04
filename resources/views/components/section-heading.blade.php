@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'center' => false,
    'level' => 2,
])

<div @class(['section-heading', 'section-heading--center' => $center]) data-reveal>
    @if($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif
    @if($level === 1)
        <h1>{{ $title }}</h1>
    @else
        <h2>{{ $title }}</h2>
    @endif
    @if($description)<p class="section-heading__description">{{ $description }}</p>@endif
</div>
