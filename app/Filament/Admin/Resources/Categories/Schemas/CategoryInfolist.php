<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('slug')->copyable(),
                        TextEntry::make('parent.name')->label('Parent category')->placeholder('Top level'),
                        TextEntry::make('sort_order'),
                        TextEntry::make('is_active')->label('Active')->badge(),
                        TextEntry::make('description')->columnSpanFull(),
                        TextEntry::make('seo_title')->label('SEO title'),
                        TextEntry::make('seo_description')->label('SEO description')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
