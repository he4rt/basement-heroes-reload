<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Providers;

use He4rt\Identity\ExternalIdentity\Contracts\IdentityDriverRegistry;
use He4rt\IntegrationSpotify\Console\PollNowPlayingCommand;
use He4rt\IntegrationSpotify\Identity\SpotifyIdentityDriver;
use He4rt\IntegrationSpotify\Models\SpotifyTrackCache;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Spotify\SpotifyExtendSocialite;

class IntegrationSpotifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/integration-spotify.php', 'integration-spotify');
    }

    public function boot(): void
    {
        Event::listen(
            SocialiteWasCalled::class,
            [SpotifyExtendSocialite::class, 'handle'],
        );

        $registry = resolve(IdentityDriverRegistry::class);
        $registry->register(new SpotifyIdentityDriver);

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        Relation::enforceMorphMap([
            'spotify_track_cache' => SpotifyTrackCache::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                PollNowPlayingCommand::class,
            ]);
        }
    }
}
