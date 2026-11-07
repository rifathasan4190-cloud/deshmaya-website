<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(160),
                        TextInput::make('slug')
                            ->maxLength(180)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('Leave blank to generate from the category name.'),
                        Select::make('parent_id')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Top-level category'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Textarea::make('description')
                            ->rows(4)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('image')
                            ->collection('image')
                            ->disk('public')
                            ->label('Category image')
                            ->image()
                            ->conversion('preview')
                            ->maxSize(10240)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Search engine listing')
                    ->schema([
                        TextInput::make('seo_title')->maxLength(70),
                        Textarea::make('seo_description')->rows(3)->maxLength(180)->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
