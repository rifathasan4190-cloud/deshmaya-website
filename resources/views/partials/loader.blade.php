@if(setting('loader.enabled', '1') == '1')
@php
    $loaderImg = setting_url('loader.image') ?: setting_url('general.logo');
    $loaderBg = preg_match('/^#[0-9a-fA-F]{6}$/', (string) setting('loader.background_color')) ? setting('loader.background_color') : null;
@endphp
<div id="site-loader" data-min="{{ (int) setting('loader.min_ms', 600) }}" data-max="{{ (int) setting('loader.max_ms', 3000) }}"
     @if($loaderBg) style="background:{{ $loaderBg }}" @endif aria-hidden="true">
    @if($loaderImg)
        <img class="loader-logo" src="{{ $loaderImg }}" alt="" width="220" height="80">
    @else
        <span class="loader-logo font-heading text-3xl text-primary">{{ setting('general.site_name', 'Deshmaya') }}</span>
    @endif
</div>
@endif
