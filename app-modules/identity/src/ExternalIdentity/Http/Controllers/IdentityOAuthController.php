<?php

declare(strict_types=1);

namespace He4rt\Identity\ExternalIdentity\Http\Controllers;

use He4rt\Identity\ExternalIdentity\Actions\ConnectIdentity;
use He4rt\Identity\ExternalIdentity\Contracts\IdentityDriverRegistry;
use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use He4rt\Identity\Teams\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IdentityOAuthController
{
    public function connect(
        IdentityProvider $provider,
        IdentityDriverRegistry $registry,
        Request $request,
    ): \Symfony\Component\HttpFoundation\RedirectResponse {
        $driver = $registry->resolve($provider);
        $returnInfo = $request->session()->get('identity_oauth_return', []);

        return $driver->redirect($returnInfo['tenant'] ?? '');
    }

    public function callback(
        IdentityProvider $provider,
        IdentityDriverRegistry $registry,
        ConnectIdentity $action,
        Request $request,
    ): RedirectResponse {
        $driver = $registry->resolve($provider);
        $credentials = $driver->callback();

        $returnInfo = $request->session()->pull('identity_oauth_return', []);
        $team = Team::query()->findOrFail($returnInfo['tenant']);

        $action->handle(
            team: $team,
            user: $request->user(),
            provider: $provider,
            credentials: $credentials,
        );

        $notification = [
            'title' => $provider->getLabel().' connected successfully',
            'status' => 'success',
        ];

        if (($returnInfo['panel'] ?? null) === 'streamer') {
            return redirect()->to(
                route('filament.streamer.pages.integrations', ['tenant' => $team])
            )->with('notification', $notification);
        }

        return to_route('filament.admin.pages.external-identities', ['tenant' => $team])
            ->with('notification', $notification);
    }
}
