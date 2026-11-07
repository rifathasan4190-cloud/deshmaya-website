@if($available && $meta)
<picture>
    <source type="image/avif" srcset="{{ $srcset('avif') }}" sizes="{{ $sizes }}">
    <source type="image/webp" srcset="{{ $srcset('webp') }}" sizes="{{ $sizes }}">
    <img src="{{ $fallback() }}" alt="{{ $alt }}" width="{{ $meta['width'] }}" height="{{ $meta['height'] }}"
         loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async" @if($eager) fetchpriority="high" @endif
         {{ $attributes }}>
</picture>
@elseif($available)
<img src="{{ $plainSrc() }}" alt="{{ $alt }}" loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async" {{ $attributes }}>
@else
<div
    {{ $attributes->class(['picture-placeholder']) }}
    @if($alt !== '') role="img" aria-label="{{ $alt }}" @else aria-hidden="true" @endif
></div>
@endif
