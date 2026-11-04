<?php

namespace App\Filament\Admin\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact message')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->url(fn (string $state): string => 'mailto:'.$state),
                        TextEntry::make('phone'),
                        TextEntry::make('subject'),
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
