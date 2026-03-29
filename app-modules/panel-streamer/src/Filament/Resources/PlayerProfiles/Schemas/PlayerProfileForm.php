<?php

declare(strict_types=1);

namespace He4rt\Streamer\Filament\Resources\PlayerProfiles\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\WidgetPlayer\Enums\AnimationType;
use He4rt\WidgetPlayer\Enums\CoverStyle;
use He4rt\WidgetPlayer\Enums\PlayerSkin;
use He4rt\WidgetPlayer\Enums\PlayerTheme;
use Illuminate\Database\Eloquent\Model;

class PlayerProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Player Profile')
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema(self::generalTab()),

                        Tab::make('Appearance')
                            ->icon('heroicon-o-paint-brush')
                            ->schema(self::appearanceTab()),

                        Tab::make('Cover & Idle')
                            ->icon('heroicon-o-photo')
                            ->schema(self::coverTab()),

                        Tab::make('Animations')
                            ->icon('heroicon-o-sparkles')
                            ->schema(self::animationsTab()),

                        Tab::make('Behavior')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema(self::behaviorTab()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private static function generalTab(): array
    {
        return [
            TextInput::make('name')
                ->label('Profile Name')
                ->required()
                ->maxLength(255),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),

            Select::make('external_identity_id')
                ->label('Spotify Connection')
                ->options(function (): array {
                    $tenant = filament()->getTenant();

                    if (!$tenant instanceof Model) {
                        return [];
                    }

                    return ExternalIdentity::query()
                        ->where('team_id', $tenant->getKey())
                        ->where('provider', IdentityProvider::Spotify)
                        ->whereNotNull('connected_at')
                        ->whereNull('disconnected_at')
                        ->pluck('external_account_id', 'id')
                        ->toArray();
                })
                ->placeholder('Select a Spotify account')
                ->helperText('Connect Spotify in External Identities to link it here.'),

            TextInput::make('browser_source_token')
                ->label('Browser Source Token')
                ->disabled()
                ->dehydrated(false)
                ->visibleOn('edit')
                ->suffixAction(
                    Action::make('copyBrowserSourceUrl')
                        ->icon('heroicon-o-clipboard-document')
                        ->tooltip('Copy OBS Browser Source URL')
                        ->action(function ($record, $livewire): void {
                            $url = route('widget-player.show', $record->browser_source_token);
                            $livewire->js(sprintf("navigator.clipboard.writeText('%s')", $url));
                            Notification::make()
                                ->title('Browser Source URL copied!')
                                ->success()
                                ->send();
                        })
                ),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private static function appearanceTab(): array
    {
        return [
            Select::make('player_skin')
                ->label('Player Skin')
                ->options(PlayerSkin::class)
                ->required()
                ->default(PlayerSkin::Compact),

            Select::make('theme')
                ->label('Theme')
                ->options(PlayerTheme::class)
                ->required()
                ->default(PlayerTheme::Dark),

            Select::make('cover_style')
                ->label('Cover Style')
                ->options(CoverStyle::class)
                ->required()
                ->default(CoverStyle::Square),

            Toggle::make('cover_glow')
                ->label('Cover Glow')
                ->default(false),

            Toggle::make('cover_blur')
                ->label('Cover Blur')
                ->default(false),

            Toggle::make('magic_colors')
                ->label('Magic Colors')
                ->helperText('Automatically extract color scheme from album cover art.')
                ->default(true)
                ->live(),

            ColorPicker::make('tint_color')
                ->label('Tint Color')
                ->visible(fn (Get $get): bool => !$get('magic_colors')),

            TextInput::make('font_family')
                ->label('Font Family')
                ->default('Poppins')
                ->maxLength(255),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private static function coverTab(): array
    {
        return [
            FileUpload::make('nothing_playing_cover_path')
                ->label('Default Cover Image')
                ->helperText('Shown when no music is playing.')
                ->image()
                ->directory('player-covers')
                ->visibility('public'),

            TextInput::make('nothing_playing_title')
                ->label('Idle Title')
                ->default('Nothing Playing')
                ->maxLength(255),

            TextInput::make('nothing_playing_artist')
                ->label('Idle Artist')
                ->default('Get the music started')
                ->maxLength(255),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private static function animationsTab(): array
    {
        return [
            Select::make('reveal_animation')
                ->label('Reveal Animation')
                ->options(AnimationType::class)
                ->required()
                ->default(AnimationType::Original),

            Select::make('exit_animation')
                ->label('Exit Animation')
                ->options(AnimationType::class)
                ->required()
                ->default(AnimationType::Original),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private static function behaviorTab(): array
    {
        return [
            Toggle::make('hide_on_pause')
                ->label('Hide on Pause')
                ->default(false)
                ->live(),

            TextInput::make('hide_delay_seconds')
                ->label('Hide Delay (seconds)')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->visible(fn (Get $get): bool => (bool) $get('hide_on_pause')),

            Toggle::make('song_change_only')
                ->label('Show Only on Song Change')
                ->default(false)
                ->live(),

            TextInput::make('visible_duration_seconds')
                ->label('Visible Duration (seconds)')
                ->numeric()
                ->minValue(1)
                ->default(10)
                ->visible(fn (Get $get): bool => (bool) $get('song_change_only')),

            Toggle::make('hide_visualizer')
                ->label('Hide Visualizer')
                ->default(false),
        ];
    }
}
