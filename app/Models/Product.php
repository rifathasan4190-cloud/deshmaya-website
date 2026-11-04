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
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['category_id', 'name', 'slug', 'short_description', 'description', 'specs', 'is_featured', 'is_active', 'sort_order', 'seo_title', 'seo_description'])]
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
