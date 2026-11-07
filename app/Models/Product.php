<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['category_id', 'name', 'slug', 'sku', 'short_description', 'description', 'specs', 'packaging_options', 'season_months', 'supplier_guide_title', 'supplier_guide_url', 'is_featured', 'is_active', 'sort_order', 'seo_title', 'seo_description'])]
class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use GeneratesUniqueSlug;

    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (self $product): void {
            $product->slug = $product->makeUniqueSlug();
        });
    }

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'specs' => 'array',
            'packaging_options' => 'array',
            'season_months' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getAvailableMediaUrl(string $collection = 'featured', array $conversions = ['preview']): ?string
    {
        return $this->getMediaUrlIfAvailable($this->getFirstMedia($collection), $conversions);
    }

    public function getMediaUrlIfAvailable(?Media $media, array $conversions = ['preview']): ?string
    {
        if (! $media) {
            return null;
        }

        $conversionDisk = Storage::disk($media->conversions_disk ?: $media->disk);

        foreach ($conversions as $conversion) {
            if ($media->hasGeneratedConversion($conversion)
                && $conversionDisk->exists($media->getPathRelativeToRoot($conversion))) {
                return $media->getUrl($conversion);
            }
        }

        return Storage::disk($media->disk)->exists($media->getPathRelativeToRoot())
            ? $media->getUrl()
            : null;
    }

    public function registerMediaCollections(): void
    {
        $acceptedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

        $this->addMediaCollection('featured')
            ->singleFile()
            ->acceptsMimeTypes($acceptedImageTypes);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes($acceptedImageTypes);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 640, 480)
            ->performOnCollections('featured', 'gallery');
    }
}
