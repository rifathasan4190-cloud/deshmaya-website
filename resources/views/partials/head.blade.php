<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@hasSection('title')@yield('title') | {{ setting('general.site_name', 'Deshmaya') }}@else{{ setting('seo.default_title', config('app.name')) }}@endif</title>
<meta name="description" content="@yield('description', setting('seo.default_description', ''))">
<link rel="canonical" href="{{ request()->url() }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:title" content="@hasSection('title')@yield('title') | {{ setting('general.site_name', 'Deshmaya') }}@else{{ setting('seo.default_title', config('app.name')) }}@endif">
<meta property="og:description" content="@yield('description', setting('seo.default_description', ''))">
@if(isset($product) && ($og = $product->getFirstMediaUrl('featured')))
    <meta property="og:image" content="{{ $og }}">
@elseif($og = setting_url('seo.og_image'))
    <meta property="og:image" content="{{ $og }}">
@endif
<meta name="twitter:card" content="summary_large_image">
@if($fav = setting_url('general.favicon'))<link rel="icon" href="{{ $fav }}">@endif
<script>
    @if(setting('loader.mode', 'session') === 'session')
    try { if (sessionStorage.getItem('loaderSeen') === '1') document.documentElement.classList.add('no-loader'); } catch (e) {}
    @endif
</script>
@include('partials.brand-style')
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
