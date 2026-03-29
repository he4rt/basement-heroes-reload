<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Identity;

use He4rt\Identity\ExternalIdentity\Capabilities\IdentityCapabilityMap;
use He4rt\Identity\ExternalIdentity\Contracts\IdentityDriver;
use He4rt\Identity\ExternalIdentity\Data\ClientAccessManager;
use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SpotifyIdentityDriver implements IdentityDriver
{
    public function provider(): IdentityProvider
    {
        return IdentityProvider::Spotify;
    }

    public function capabilities(): IdentityCapabilityMap
    {
        return IdentityCapabilityMap::make();
    }

    public function redirect(string $tenantId): RedirectResponse
    {
        return Socialite::driver('spotify')
            ->scopes(config('services.spotify.scopes'))
            ->redirect();
    }

    public function callback(): ClientAccessManager
    {
        $user = Socialite::driver('spotify')->user();

        return new ClientAccessManager(
            accessToken: Crypt::encrypt($user->token),
            refreshToken: Crypt::encrypt($user->refreshToken),
            expiresIn: Crypt::encrypt((string) $user->expiresIn),
        );
    }
}
