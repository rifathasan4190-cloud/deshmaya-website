<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('slug')->copyable(),
                        TextEntry::make('category.name')->label('Category'),
                        TextEntry::make('is_active')->label('Active')->badge(),
                        TextEntry::make('is_featured')->label('Featured')->badge(),
                        TextEntry::make('short_description')->columnSpanFull(),
                        TextEntry::make('description')->columnSpanFull(),
                        TextEntry::make('specs')
                            ->formatStateUsing(fn (?array $state): string => collect($state ?? [])->map(fn ($value, $key): string => "{$key}: {$value}")->implode("\n"))
                            ->columnSpanFull(),
                        TextEntry::make('seo_title')->label('SEO title'),
                        TextEntry::make('seo_description')->label('SEO description')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
