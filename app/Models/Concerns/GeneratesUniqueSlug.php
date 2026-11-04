<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * @property string|null $slug
 * @property string $name
 */
trait GeneratesUniqueSlug
{
    protected function makeUniqueSlug(): string
    {
        $baseSlug = Str::slug($this->slug ?: $this->name) ?: Str::slug(class_basename($this));
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->newQueryWithoutScopes()
            ->where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
            ->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
