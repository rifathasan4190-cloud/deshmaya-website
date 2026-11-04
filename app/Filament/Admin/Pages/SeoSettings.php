<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\SettingsPage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class SeoSettings extends SettingsPage
{
    protected static string $settingsGroup = 'seo';

    protected static ?string $navigationLabel = 'SEO Defaults';

    protected static ?string $title = 'SEO Defaults';

    protected static ?int $navigationSort = 4;

    protected function formComponents(): array
    {
        return [
            Section::make('Search and social defaults')
                ->schema([
                    TextInput::make('default_title')->label('Default page title')->maxLength(70)->required()->columnSpanFull(),
                    Textarea::make('default_description')->label('Default meta description')->rows(3)->maxLength(180)->columnSpanFull(),
                    FileUpload::make('og_image')->label('Open Graph image')->image()->disk('public')->directory('site')->visibility('public')->maxSize(8192)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Analytics')
                ->schema([
                    TextInput::make('google_analytics_id')->label('Google Analytics ID')->maxLength(80),
                    TextInput::make('meta_pixel_id')->label('Meta Pixel ID')->maxLength(80),
                ])
                ->columns(2),
        ];
    }
}
