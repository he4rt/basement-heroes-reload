<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Enums\FilamentPanel;
use App\Filament\Shared\Pages\LoginPage;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use He4rt\Identity\Teams\Team;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class StreamerPanelProvider extends PanelProvider
{
    private FilamentPanel $panelId = FilamentPanel::Streamer;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->path($this->panelId->value)
            ->id($this->panelId->value)
            ->login(LoginPage::class)
            ->tenant(Team::class)
            ->topbar(false)
            ->sidebarFullyCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Emerald,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'danger' => Color::Red,
                'info' => Color::Indigo,
                'gray' => Color::Gray,
            ])
            ->viteTheme(sprintf('resources/css/filament/%s/theme.css', $this->panelId->value))
            ->defaultThemeMode(config('sycorax.filament.theme_mode'))
            ->discoverResources(in: modules_path('panel-streamer/src/Filament/Resources'), for: 'He4rt\\Streamer\\Filament\\Resources')
            ->discoverPages(in: modules_path('panel-streamer/src/Filament/Pages'), for: 'He4rt\\Streamer\\Filament\\Pages')
            ->discoverWidgets(in: modules_path('panel-streamer/src/Filament/Widgets'), for: 'He4rt\\Streamer\\Filament\\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
