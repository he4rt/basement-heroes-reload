<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Http\Controllers;

use He4rt\WidgetPlayer\Models\PlayerProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WidgetPlayerController
{
    public function __invoke(Request $request, string $token): View
    {
        $profile = PlayerProfile::query()
            ->where('browser_source_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        return view('widget-player::player', [
            'profile' => $profile,
        ]);
    }
}
