@php
    $sameAs = collect(['facebook', 'instagram', 'linkedin', 'youtube', 'x', 'tiktok'])
        ->map(fn (string $network) => setting("social.$network"))
        ->filter()
        ->values();
    $logo = setting_url('general.logo', asset('images/logo.png'));
    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => setting('general.site_name', 'Deshmaya'),
        'url' => route('home'),
        'description' => setting('seo.default_description', ''),
        'logo' => $logo ?: null,
        'email' => setting('contact.email_primary') ?: null,
        'telephone' => setting('contact.phone_primary') ?: null,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => setting('contact.address', 'Dhaka, Bangladesh'),
            'addressCountry' => 'BD',
        ],
        'sameAs' => $sameAs->all() ?: null,
    ], fn ($value) => $value !== null && $value !== '');
@endphp
<script type="application/ld+json">@json($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
