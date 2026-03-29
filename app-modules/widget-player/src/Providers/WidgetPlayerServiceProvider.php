<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Providers;

use He4rt\WidgetPlayer\Models\PlayerProfile;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class WidgetPlayerServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        foreach (glob(__DIR__.'/../../database/migrations/*') as $directory) {
            $this->loadMigrationsFrom($directory);
        }

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'widget-player');
        $this->loadRoutesFrom(__DIR__.'/../../resources/routes/web.php');

        Relation::enforceMorphMap([
            'player_profile' => PlayerProfile::class,
        ]);
    }
}
