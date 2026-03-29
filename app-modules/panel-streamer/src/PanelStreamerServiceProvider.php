<?php

declare(strict_types=1);

namespace He4rt\Streamer;

use Illuminate\Support\ServiceProvider;

class PanelStreamerServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'panel-streamer');
    }
}
