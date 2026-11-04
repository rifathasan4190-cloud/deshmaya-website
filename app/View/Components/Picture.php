<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * <x-picture src="hero/mango.jpg" alt="Mango" sizes="(min-width:1024px) 50vw, 100vw" :eager="true" class="..." />
 * src is relative to resources/images; variants come from `npm run images` (public/images/manifest.json).
 */
class Picture extends Component
{
    public ?array $meta;

    public function __construct(
        public string $src,
        public string $alt = '',
        public string $sizes = '100vw',
        public bool $eager = false,
    ) {
        $this->meta = self::manifest()[ltrim($src, '/')] ?? null;
    }

    public static function manifest(): array
    {
        static $manifest = null;

        return $manifest ??= is_file($p = public_path('images/manifest.json'))
            ? (json_decode(file_get_contents($p), true) ?: [])
            : [];
    }

    public function srcset(string $format): string
    {
        return collect($this->meta['widths'])
            ->map(fn ($w) => "/images/{$this->meta['base']}-{$w}.{$format} {$w}w")
            ->implode(', ');
    }

    public function fallback(): string
    {
        $widths = collect($this->meta['widths']);
        $w = $widths->first(fn ($w) => $w >= 1024) ?? $widths->last();

        return "/images/{$this->meta['base']}-{$w}.webp";
    }

    public function plainSrc(): string
    {
        return (str_starts_with($this->src, 'http') || str_starts_with($this->src, '/')) ? $this->src : asset($this->src);
    }

    public function render()
    {
        return view('components.picture');
    }
}
