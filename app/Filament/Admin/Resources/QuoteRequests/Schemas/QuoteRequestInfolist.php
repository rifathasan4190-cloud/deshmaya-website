<?php

namespace App\Filament\Admin\Resources\QuoteRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quote request')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('company'),
                        TextEntry::make('email')->url(fn (string $state): string => 'mailto:'.$state),
                        TextEntry::make('phone'),
                        TextEntry::make('country'),
                        TextEntry::make('product_name')->label('Product'),
                        TextEntry::make('quantity'),
                        TextEntry::make('unit'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('created_at')->dateTime()->label('Received'),
                        TextEntry::make('message')->columnSpanFull(),
                        TextEntry::make('ip')->label('IP address'),
                        TextEntry::make('user_agent')->label('Browser')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
