<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Actions;

use He4rt\Identity\ExternalIdentity\Data\ClientAccessManager;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\IntegrationSpotify\Connectors\SpotifyConnector;
use Illuminate\Support\Facades\Crypt;
use Saloon\Http\Auth\AccessTokenAuthenticator;

class RefreshSpotifyToken
{
    public function handle(ExternalIdentity $identity): AccessTokenAuthenticator
    {
        $connector = new SpotifyConnector;
        $credentials = $identity->credentials;

        $oldAuthenticator = new AccessTokenAuthenticator(
            accessToken: $credentials->getAccessToken(),
            refreshToken: $credentials->getRefreshToken(),
        );

        $newAuthenticator = $connector->refreshAccessToken($oldAuthenticator);

        $identity->update([
            'credentials' => new ClientAccessManager(
                accessToken: Crypt::encrypt($newAuthenticator->getAccessToken()),
                refreshToken: Crypt::encrypt($newAuthenticator->getRefreshToken()),
                expiresIn: Crypt::encrypt((string) 3600),
            ),
        ]);

        return $newAuthenticator;
    }
}
