<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Actions;

use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\IntegrationSpotify\Connectors\SpotifyConnector;
use He4rt\IntegrationSpotify\Data\CurrentlyPlayingData;
use He4rt\IntegrationSpotify\Requests\GetCurrentlyPlayingRequest;
use Saloon\Http\Auth\AccessTokenAuthenticator;

class GetCurrentlyPlaying
{
    public function __construct(
        private readonly RefreshSpotifyToken $refreshToken,
    ) {}

    public function handle(ExternalIdentity $identity): CurrentlyPlayingData
    {
        $connector = $this->makeConnector($identity);
        $response = $connector->send(new GetCurrentlyPlayingRequest);

        if ($response->status() === 204) {
            return CurrentlyPlayingData::empty();
        }

        if ($response->status() === 401) {
            $authenticator = $this->refreshToken->handle($identity);
            $connector = new SpotifyConnector;
            $connector->authenticate($authenticator);
            $response = $connector->send(new GetCurrentlyPlayingRequest);
        }

        if ($response->status() === 429) {
            $retryAfter = (int) $response->header('Retry-After');
            logger()->warning(sprintf('Spotify rate limited. Retry after %ds', $retryAfter), [
                'identity_id' => $identity->id,
            ]);

            return CurrentlyPlayingData::empty();
        }

        $response->throw();

        return CurrentlyPlayingData::fromApiResponse($response->json());
    }

    private function makeConnector(ExternalIdentity $identity): SpotifyConnector
    {
        $connector = new SpotifyConnector;
        $credentials = $identity->credentials;

        $connector->authenticate(new AccessTokenAuthenticator(
            accessToken: $credentials->getAccessToken(),
            refreshToken: $credentials->getRefreshToken(),
        ));

        return $connector;
    }
}
