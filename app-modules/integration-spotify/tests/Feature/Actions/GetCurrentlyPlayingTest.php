<?php

declare(strict_types=1);

use He4rt\Identity\ExternalIdentity\Data\ClientAccessManager;
use He4rt\Identity\ExternalIdentity\Enums\CredentialsType;
use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use He4rt\Identity\ExternalIdentity\Enums\IdentityType;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\Identity\Teams\Team;
use He4rt\Identity\Users\User;
use He4rt\IntegrationSpotify\Actions\GetCurrentlyPlaying;
use He4rt\IntegrationSpotify\Data\CurrentlyPlayingData;
use Illuminate\Support\Facades\Crypt;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

describe('GetCurrentlyPlaying action', function (): void {
    beforeEach(function (): void {
        config([
            'services.spotify.client_id' => 'test-client-id',
            'services.spotify.client_secret' => 'test-client-secret',
            'services.spotify.redirect' => 'http://127.0.0.1:8000/identity/spotify/callback',
        ]);

        $this->team = Team::factory()->create();
        $this->user = User::factory()->create();

        $this->identity = ExternalIdentity::query()->create([
            'team_id' => $this->team->id,
            'type' => IdentityType::External,
            'provider' => IdentityProvider::Spotify,
            'credentials_type' => CredentialsType::OAuth2,
            'credentials' => new ClientAccessManager(
                accessToken: Crypt::encrypt('test-access-token'),
                refreshToken: Crypt::encrypt('test-refresh-token'),
                expiresIn: Crypt::encrypt('3600'),
            ),
            'connected_by' => $this->user->id,
            'connected_at' => now(),
        ]);
    });

    test('returns currently playing data on 200 response', function (): void {
        MockClient::global([
            MockResponse::make([
                'is_playing' => true,
                'progress_ms' => 30000,
                'item' => [
                    'id' => 'track123',
                    'name' => 'Test Song',
                    'duration_ms' => 200000,
                    'artists' => [['name' => 'Test Artist']],
                    'album' => [
                        'name' => 'Test Album',
                        'images' => [['url' => 'https://example.com/image.jpg', 'height' => 640, 'width' => 640]],
                    ],
                ],
            ], 200),
        ]);

        $action = resolve(GetCurrentlyPlaying::class);
        $result = $action->handle($this->identity);

        expect($result)
            ->toBeInstanceOf(CurrentlyPlayingData::class)
            ->trackId->toBe('track123')
            ->trackName->toBe('Test Song')
            ->artistName->toBe('Test Artist')
            ->isPlaying->toBeTrue();

        MockClient::destroyGlobal();
    });

    test('returns empty data on 204 response (nothing playing)', function (): void {
        MockClient::global([
            MockResponse::make('', 204),
        ]);

        $action = resolve(GetCurrentlyPlaying::class);
        $result = $action->handle($this->identity);

        expect($result)
            ->toBeInstanceOf(CurrentlyPlayingData::class)
            ->trackId->toBeNull()
            ->isPlaying->toBeFalse();

        MockClient::destroyGlobal();
    });

    test('returns empty data on 429 rate limit response', function (): void {
        MockClient::global([
            MockResponse::make('', 429, ['Retry-After' => '30']),
        ]);

        $action = resolve(GetCurrentlyPlaying::class);
        $result = $action->handle($this->identity);

        expect($result)
            ->toBeInstanceOf(CurrentlyPlayingData::class)
            ->trackId->toBeNull()
            ->isPlaying->toBeFalse();

        MockClient::destroyGlobal();
    });

    test('refreshes token and retries on 401 response', function (): void {
        MockClient::global([
            // First request: 401 unauthorized
            MockResponse::make('', 401),
            // Refresh token request
            MockResponse::make([
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            // Retry request: success
            MockResponse::make([
                'is_playing' => true,
                'progress_ms' => 10000,
                'item' => [
                    'id' => 'track789',
                    'name' => 'Retried Song',
                    'duration_ms' => 180000,
                    'artists' => [['name' => 'Retry Artist']],
                    'album' => [
                        'name' => 'Retry Album',
                        'images' => [['url' => 'https://example.com/retry.jpg', 'height' => 640, 'width' => 640]],
                    ],
                ],
            ], 200),
        ]);

        $action = resolve(GetCurrentlyPlaying::class);
        $result = $action->handle($this->identity);

        expect($result)
            ->trackId->toBe('track789')
            ->trackName->toBe('Retried Song')
            ->isPlaying->toBeTrue();

        // Verify credentials were updated
        $this->identity->refresh();
        expect($this->identity->credentials->getAccessToken())->toBe('new-access-token');

        MockClient::destroyGlobal();
    });
});
