<?php

declare(strict_types=1);

use He4rt\WidgetPlayer\Http\Controllers\WidgetPlayerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('widget')
    ->group(function () {
        Route::get('player/{token}', WidgetPlayerController::class)
            ->name('widget-player.show');
    });
