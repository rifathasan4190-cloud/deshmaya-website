<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\SettingsPage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;

class BrandColors extends SettingsPage
{
    protected static string $settingsGroup = 'brand';

    protected static ?string $navigationLabel = 'Brand Colors';

    protected static ?string $title = 'Brand Colors';

    protected static ?int $navigationSort = 1;

    protected function hasResetAction(): bool
    {
        return true;
    }

    protected function formComponents(): array
    {
        $colors = [
            'primary' => 'Primary',
            'primary_dark' => 'Primary dark',
            'secondary' => 'Secondary',
            'accent' => 'Accent',
            'background' => 'Background',
            'surface' => 'Surface',
            'text' => 'Text',
            'text_muted' => 'Muted text',
            'border' => 'Border',
            'success' => 'Success',
            'danger' => 'Danger',
        ];

        return [
            Section::make('Theme palette')
                ->description('Changes are applied to the public site after saving.')
                ->schema([
                    Placeholder::make('live_preview')
                        ->label('Live preview')
                        ->content(function (Get $get): HtmlString {
                            $palette = [
                                'Primary' => $get('primary'),
                                'Secondary' => $get('secondary'),
                                'Accent' => $get('accent'),
                                'Text' => $get('text'),
                            ];

                            $swatches = collect($palette)->map(function ($value, $label): string {
                                $color = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) ? $value : 'transparent';

                                return '<span style="display:inline-flex;align-items:center;gap:8px;margin:0 12px 8px 0">'
                                    .'<span style="width:24px;height:24px;border:1px solid #999;background:'.e($color).'"></span>'
                                    .e($label).'</span>';
                            })->implode('');

                            return new HtmlString('<div>'.$swatches.'</div>');
                        })
                        ->columnSpanFull(),
                    ...collect($colors)->map(fn (string $label, string $key) => ColorPicker::make($key)
                        ->label($label)
                        ->hex()
                        ->required()
                        ->rules(['regex:/^#[0-9a-fA-F]{6}$/'])
                        ->live())->all(),
                ])
                ->columns(2),
        ];
    }
}
