<?php

namespace TomatoPHP\FilamentSlackDriver\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use TomatoPHP\FilamentSettingsHub\Pages\SettingsHub;
use TomatoPHP\FilamentSlackDriver\Settings\SlackSettings;

class SlackSettingsPage extends SettingsPage
{
    protected static BackedEnum | null | string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = SlackSettings::class;

    /**
     * Settings that hold a credential and must never be sent back to the browser.
     *
     * @var array<int, string>
     */
    protected const SECRETS = [
        'slack_webhook',
        'slack_token',
    ];

    public function getTitle(): string
    {
        return trans('filament-slack-driver::messages.settings.slack.title');
    }

    protected function getActions(): array
    {
        return [
            Action::make('back')->url(SettingsHub::getUrl()),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function form(Schema $form): Schema
    {
        return $form->columns(1)
            ->schema([
                Section::make()->schema([
                    TextInput::make('slack_webhook')
                        ->password()
                        ->revealable(false)
                        ->autocomplete('new-password')
                        ->label(trans('filament-slack-driver::messages.settings.slack.webhook'))
                        ->helperText(trans('filament-slack-driver::messages.settings.slack.keep_secret'))
                        ->hint(config('filament-alerts.show_hint') ? 'setting("slack_webhook")' : null),
                    TextInput::make('slack_token')
                        ->password()
                        ->revealable(false)
                        ->autocomplete('new-password')
                        ->label(trans('filament-slack-driver::messages.settings.slack.token'))
                        ->helperText(trans('filament-slack-driver::messages.settings.slack.keep_secret'))
                        ->hint(config('filament-alerts.show_hint') ? 'setting("slack_token")' : null),
                    TextInput::make('slack_channel')
                        ->label(trans('filament-slack-driver::messages.settings.slack.channel'))
                        ->helperText(trans('filament-slack-driver::messages.settings.slack.channel_help'))
                        ->hint(config('filament-alerts.show_hint') ? 'setting("slack_channel")' : null),
                    Toggle::make('slack_active')
                        ->label(trans('filament-slack-driver::messages.settings.slack.active'))
                        ->hint(config('filament-alerts.show_hint') ? 'setting("slack_active")' : null),
                ]),
            ]);
    }

    /**
     * Secrets never leave the server, the password inputs always start empty.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (static::SECRETS as $secret) {
            $data[$secret] = null;
        }

        return $data;
    }

    /**
     * A blank secret input keeps the value already stored.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $settings = app(static::getSettings());

        foreach (static::SECRETS as $secret) {
            if (blank($data[$secret] ?? null)) {
                $data[$secret] = $settings->{$secret};
            }
        }

        return $data;
    }

    public function afterSave(): void
    {
        Artisan::call('cache:clear');
    }
}
