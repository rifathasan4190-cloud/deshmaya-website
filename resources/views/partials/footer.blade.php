<footer class="site-footer">
    <div class="container-shell site-footer__main">
        <div class="site-footer__brand">
            <a href="{{ route('home') }}" class="brand-lockup brand-lockup--footer">
                @if($logo = setting_url('general.logo'))
                    <img src="{{ $logo }}" alt="{{ setting('general.site_name', 'Deshmaya') }}" class="brand-lockup__image" width="172" height="48" loading="lazy">
                @else
                    <span class="brand-lockup__mark" aria-hidden="true">D</span>
                    <span class="brand-lockup__text"><strong>{{ setting('general.site_name', 'Deshmaya') }}</strong><small>Global Agri Exporter</small></span>
                @endif
            </a>
            @if($tagline = setting('general.tagline'))<p class="site-footer__tagline">{{ $tagline }}</p>@endif
            @if($footerText = setting('general.footer_text'))<p class="site-footer__copy">{{ $footerText }}</p>@else<p class="site-footer__copy">Fresh agricultural products, sourced in Bangladesh and prepared for global markets.</p>@endif
        </div>

        <div class="site-footer__column">
            <h2>Explore</h2>
            <nav aria-label="Footer navigation">
                @foreach(config('site.menu') as $item)
                    @if($item['enabled'] && $item['route'])
                        <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="site-footer__column">
            <h2>Get in touch</h2>
            <div class="site-footer__contact">
                @if($address = setting('contact.address'))<p>{{ $address }}</p>@endif
                @if($phone = setting('contact.phone_primary'))<a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a>@endif
                @if($email = setting('contact.email_primary'))<a href="mailto:{{ $email }}">{{ $email }}</a>@endif
                <a class="site-footer__quote" href="{{ route('request-quote') }}">Request a Quote <span aria-hidden="true">→</span></a>
            </div>
            <div class="social-links social-links--footer">
                @foreach(['facebook','instagram','linkedin','youtube','x','tiktok'] as $network)
                    @if($url = setting("social.$network"))
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}">{{ strtoupper(substr($network, 0, 1)) }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container-shell">
            <p>{{ setting('general.copyright_text') ?: '© '.date('Y').' '.setting('general.site_name', 'Deshmaya').'. All rights reserved.' }}</p>
            <a href="{{ route('contact') }}">Contact our team</a>
        </div>
    </div>
</footer>
