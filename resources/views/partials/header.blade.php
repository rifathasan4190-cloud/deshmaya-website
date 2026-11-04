<header class="site-header" x-data="{ open: false }">
    <div class="container-shell site-header__inner">
        <a href="{{ route('home') }}" class="brand-lockup" aria-label="{{ setting('general.site_name', 'Deshmaya') }} home">
            @if($logo = setting_url('general.logo'))
                <img src="{{ $logo }}" alt="{{ setting('general.site_name', 'Deshmaya') }}" class="brand-lockup__image" width="172" height="48">
            @else
                <span class="brand-lockup__mark" aria-hidden="true">D</span>
                <span class="brand-lockup__text"><strong>{{ setting('general.site_name', 'Deshmaya') }}</strong><small>Global Agri Exporter</small></span>
            @endif
        </a>

        <nav class="site-nav" aria-label="Main navigation">
            @foreach(config('site.menu') as $item)
                @if($item['enabled'] && $item['route'])
                    <a href="{{ route($item['route']) }}" @class(['site-nav__link', 'is-active' => request()->routeIs($item['route'])]) @if(request()->routeIs($item['route'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endif
            @endforeach
        </nav>

        <a href="{{ route('request-quote') }}" class="button button--header">Request a Quote <span aria-hidden="true">→</span></a>
        <button class="site-menu-toggle" type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-navigation" :aria-label="open ? 'Close menu' : 'Open menu'">
            <span></span><span></span><span></span>
        </button>
    </div>

    <nav id="mobile-navigation" class="mobile-nav" x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        @click.outside="open = false" @keydown.escape.window="open = false" aria-label="Mobile navigation">
        <div class="mobile-nav__heading">
            <span class="eyebrow">Explore</span>
            <strong>Discover Deshmaya</strong>
        </div>
        @foreach(config('site.menu') as $item)
            @if($item['enabled'] && $item['route'])
                <a href="{{ route($item['route']) }}" @click="open = false" @class(['mobile-nav__link', 'is-active' => request()->routeIs($item['route'])]) @if(request()->routeIs($item['route'])) aria-current="page" @endif>
                    <span>{{ $item['label'] }}</span><span class="mobile-nav__arrow" aria-hidden="true">→</span>
                </a>
            @endif
        @endforeach
        <a href="{{ route('request-quote') }}" class="button button--primary mobile-nav__cta" @click="open = false">Request a Quote <span aria-hidden="true">→</span></a>
    </nav>
</header>
