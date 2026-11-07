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
                        TextEntry::make('sku')->label('SKU'),
                        TextEntry::make('category.name')->label('Category'),
                        TextEntry::make('is_active')->label('Active')->badge(),
                        TextEntry::make('is_featured')->label('Featured')->badge(),
                        TextEntry::make('short_description')->columnSpanFull(),
                        TextEntry::make('description')->columnSpanFull(),
                        TextEntry::make('specs')
                            ->formatStateUsing(fn (?array $state): string => collect($state ?? [])->map(fn ($value, $key): string => "{$key}: {$value}")->implode("\n"))
                            ->columnSpanFull(),
                        TextEntry::make('packaging_options')
                            ->label('Packaging options')
                            ->formatStateUsing(fn (?array $state): string => collect($state ?? [])
                                ->map(fn (array $option): string => collect([$option['name'] ?? null, $option['dimensions'] ?? null])->filter()->implode(' — '))
                                ->implode("\n"))
                            ->columnSpanFull(),
                        TextEntry::make('season_months')
                            ->label('Available months')
                            ->formatStateUsing(fn (?array $state): string => collect($state ?? [])
                                ->map(fn (int|string $month): string => date('F', mktime(0, 0, 0, (int) $month, 1)))
                                ->implode(', '))
                            ->columnSpanFull(),
                        TextEntry::make('supplier_guide_title')->label('Supplier guide link text'),
                        TextEntry::make('supplier_guide_url')->label('Supplier guide URL')->url()->openUrlInNewTab(),
                        TextEntry::make('seo_title')->label('SEO title'),
                        TextEntry::make('seo_description')->label('SEO description')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
