<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['parent_id', 'name', 'slug', 'description', 'sort_order', 'is_active', 'seo_title', 'seo_description'])]
class Category extends Model implements HasMedia
{
    /** @use HasFactory<CategoryFactory> */
    use GeneratesUniqueSlug;

    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (self $category): void {
            $category->slug = $category->makeUniqueSlug();

            if (blank($category->parent_id)) {
                return;
            }

            $ancestor = static::withTrashed()->find($category->parent_id);
            $visited = [];

            while ($ancestor) {
                if ($ancestor->is($category)) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'A category cannot be nested under itself or its descendants.',
                    ]);
                }

                if (isset($visited[$ancestor->getKey()])) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'The selected parent already belongs to a circular category hierarchy.',
                    ]);
                }

                $visited[$ancestor->getKey()] = true;
                $ancestor = $ancestor->parent;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 640, 480)
            ->performOnCollections('image');
    }
}
