<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\SettingsPage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class BusinessInfo extends SettingsPage
{
    protected static string $settingsGroup = 'general';

    protected static ?string $navigationLabel = 'Business & Connect Info';

    protected static ?string $title = 'Business & Connect Info';

    protected static ?int $navigationSort = 2;

    protected function settingGroups(): array
    {
        return ['general', 'contact', 'social', 'mail'];
    }

    protected function formComponents(): array
    {
        return [
            Tabs::make('Business settings')
                ->tabs([
                    Tab::make('Business')
                        ->schema([
                            TextInput::make('site_name')->label('Business name')->required()->maxLength(100),
                            TextInput::make('tagline')->maxLength(160),
                            FileUpload::make('logo')->label('Logo (light)')->image()->disk('public')->directory('site')->visibility('public')->maxSize(4096),
                            FileUpload::make('logo_dark')->label('Logo (dark)')->image()->disk('public')->directory('site')->visibility('public')->maxSize(4096),
                            FileUpload::make('favicon')->image()->disk('public')->directory('site')->visibility('public')->maxSize(4096),
                            Textarea::make('footer_text')->rows(3)->maxLength(500),
                            TextInput::make('copyright_text')->maxLength(200),
                        ])
                        ->columns(2),
                    Tab::make('Contact')
                        ->schema([
                            TextInput::make('phone_primary')->tel(),
                            TextInput::make('phone_secondary')->tel(),
                            TextInput::make('whatsapp')->tel(),
                            TextInput::make('email_primary')->email(),
                            TextInput::make('email_secondary')->email(),
                            TextInput::make('address')->maxLength(500)->columnSpanFull(),
                            Textarea::make('map_embed')->label('Map embed URL or code')->rows(3)->columnSpanFull(),
                            TextInput::make('working_hours')->maxLength(200)->columnSpanFull(),
                        ])
                        ->columns(2),
                    Tab::make('Social')
                        ->schema([
                            TextInput::make('facebook')->url(),
                            TextInput::make('instagram')->url(),
                            TextInput::make('linkedin')->url(),
                            TextInput::make('youtube')->url(),
                            TextInput::make('x')->label('X')->url(),
                            TextInput::make('tiktok')->url(),
                        ])
                        ->columns(2),
                    Tab::make('Mail')
                        ->schema([
                            TextInput::make('notification_email')->label('Admin notification email')->email(),
                            TextInput::make('from_name')->label('Email sender name')->maxLength(100),
                        ])
                        ->columns(2),
                ])
                ->columnSpanFull(),
        ];
    }
}
