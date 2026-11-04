<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\SettingsPage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class LoaderSettings extends SettingsPage
{
    protected static string $settingsGroup = 'loader';

    protected static ?string $navigationLabel = 'Loader';

    protected static ?string $title = 'Loader Settings';

    protected static ?int $navigationSort = 3;

    protected function formComponents(): array
    {
        return [
            Section::make('Page loader')
                ->schema([
                    Toggle::make('enabled')
                        ->label('Show loader')
                        ->formatStateUsing(fn ($state): bool => (bool) $state)
                        ->dehydrateStateUsing(fn ($state): string => $state ? '1' : '0'),
                    ColorPicker::make('background_color')
                        ->label('Background color')
                        ->hex()
                        ->required()
                        ->rules(['regex:/^#[0-9a-fA-F]{6}$/']),
                    FileUpload::make('image')
                        ->label('Loader logo or image')
                        ->image()
                        ->disk('public')
                        ->directory('site')
                        ->visibility('public')
                        ->maxSize(4096)
                        ->columnSpanFull(),
                    TextInput::make('min_ms')->label('Minimum display time (ms)')->numeric()->integer()->minValue(0)->maxValue(10000)->required(),
                    TextInput::make('max_ms')->label('Maximum display time (ms)')->numeric()->integer()->minValue(100)->maxValue(30000)->required(),
                ])
                ->columns(2),
        ];
    }
}
