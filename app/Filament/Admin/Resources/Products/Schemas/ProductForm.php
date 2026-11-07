<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product details')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(180),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        TextInput::make('slug')
                            ->maxLength(200)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('Leave blank to generate from the product name.'),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')->label('Active')->default(true),
                        Toggle::make('is_featured')->label('Featured'),
                        TextInput::make('short_description')->maxLength(500)->columnSpanFull(),
                        Textarea::make('description')->rows(7)->maxLength(20000)->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Product media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->collection('featured')
                            ->disk('public')
                            ->label('Featured image')
                            ->image()
                            ->conversion('preview')
                            ->maxSize(10240),
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->collection('gallery')
                            ->disk('public')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->conversion('preview')
                            ->maxFiles(20)
                            ->maxSize(10240),
                    ])
                    ->columns(2),
                Section::make('Packaging options')
                    ->schema([
                        Repeater::make('packaging_options')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Packaging type')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('dimensions')
                                    ->label('Dimensions')
                                    ->placeholder('60 x 40 x 15 cm')
                                    ->maxLength(100),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add packaging option')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Season and supplier guide')
                    ->schema([
                        CheckboxList::make('season_months')
                            ->label('Available months')
                            ->options([
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December',
                            ])
                            ->columns(4)
                            ->bulkToggleable()
                            ->columnSpanFull(),
                        TextInput::make('supplier_guide_title')
                            ->label('Supplier guide link text')
                            ->maxLength(180),
                        TextInput::make('supplier_guide_url')
                            ->label('Supplier guide URL')
                            ->url()
                            ->maxLength(2048),
                    ])
                    ->columns(2),
                Section::make('Specifications')
                    ->schema([
                        KeyValue::make('specs')
                            ->keyLabel('Specification')
                            ->valueLabel('Value')
                            ->reorderable()
                            ->addActionLabel('Add specification')
                            ->columnSpanFull(),
                    ]),
                Section::make('Search engine listing')
                    ->schema([
                        TextInput::make('seo_title')->maxLength(70),
                        Textarea::make('seo_description')->rows(3)->maxLength(180)->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
