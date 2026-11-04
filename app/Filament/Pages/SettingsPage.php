<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

abstract class SettingsPage extends Page
{
    public ?array $data = [];

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static string $settingsGroup;

    public function mount(SettingService $settings): void
    {
        $data = $this->defaultValues();

        foreach ($this->settingGroups() as $group) {
            $data = array_replace($data, $settings->group($group));
        }

        $this->form->fill($data);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components($this->formComponents());
    }

    public function content(Schema $schema): Schema
    {
        $actions = [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];

        if ($this->hasResetAction()) {
            $actions[] = Action::make('reset')
                ->label('Reset to defaults')
                ->color('gray')
                ->requiresConfirmation()
                ->action(fn () => $this->resetToDefaults());
        }

        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([Actions::make($actions)]),
        ]);
    }

    public function save(SettingService $settings): void
    {
        foreach ($this->form->getState() as $key => $value) {
            $group = $this->settingGroupForKey($key);
            $type = config("site-settings.groups.{$group}.type", 'text');

            $settings->set("{$group}.{$key}", $value, $type);
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    public function resetToDefaults(): void
    {
        $this->form->fill($this->defaultValues());
        $this->save(app(SettingService::class));
    }

    protected function defaultValues(): array
    {
        $values = [];

        foreach ($this->settingGroups() as $group) {
            $values = array_replace($values, config("site-settings.groups.{$group}.values", []));
        }

        return $values;
    }

    protected function settingGroups(): array
    {
        return [static::$settingsGroup];
    }

    protected function settingGroupForKey(string $key): string
    {
        foreach ($this->settingGroups() as $group) {
            if (array_key_exists($key, config("site-settings.groups.{$group}.values", []))) {
                return $group;
            }
        }

        return static::$settingsGroup;
    }

    protected function hasResetAction(): bool
    {
        return false;
    }

    abstract protected function formComponents(): array;
}
