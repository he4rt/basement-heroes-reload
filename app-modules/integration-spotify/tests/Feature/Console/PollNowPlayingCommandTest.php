<?php

declare(strict_types=1);

use He4rt\Identity\ExternalIdentity\Data\ClientAccessManager;
use He4rt\Identity\ExternalIdentity\Enums\CredentialsType;
use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use He4rt\Identity\ExternalIdentity\Enums\IdentityType;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\Identity\Teams\Team;
use He4rt\Identity\Users\User;
use He4rt\IntegrationSpotify\Jobs\FetchNowPlaying;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Queue;

describe('PollNowPlayingCommand', function (): void {
    test('dispatches jobs for active Spotify connections only', function (): void {
        Queue::fake([FetchNowPlaying::class]);

        $team = Team::factory()->create();
        $user = User::factory()->create();

        $credentials = new ClientAccessManager(
            accessToken: Crypt::encrypt('test-token'),
            refreshToken: Crypt::encrypt('test-refresh'),
            expiresIn: Crypt::encrypt('3600'),
        );

        // Active Spotify connection
        ExternalIdentity::query()->create([
            'team_id' => $team->id,
            'type' => IdentityType::External,
            'provider' => IdentityProvider::Spotify,
            'credentials_type' => CredentialsType::OAuth2,
            'credentials' => $credentials,
            'connected_by' => $user->id,
            'connected_at' => now(),
        ]);

        // Disconnected Spotify connection (different team)
        $team2 = Team::factory()->create();
        ExternalIdentity::query()->create([
            'team_id' => $team2->id,
            'type' => IdentityType::External,
            'provider' => IdentityProvider::Spotify,
            'credentials_type' => CredentialsType::OAuth2,
            'credentials' => $credentials,
            'connected_by' => $user->id,
            'connected_at' => now(),
            'disconnected_at' => now(),
        ]);

        // GitHub connection (should be ignored)
        $team3 = Team::factory()->create();
        ExternalIdentity::query()->create([
            'team_id' => $team3->id,
            'type' => IdentityType::External,
            'provider' => IdentityProvider::GitHub,
            'credentials_type' => CredentialsType::OAuth2,
            'credentials' => $credentials,
            'connected_by' => $user->id,
            'connected_at' => now(),
        ]);

        $this->artisan('spotify:poll-now-playing')->assertSuccessful();

        Queue::assertPushed(FetchNowPlaying::class, 1);
    });

    test('dispatches no jobs when no active connections exist', function (): void {
        Queue::fake([FetchNowPlaying::class]);

        $this->artisan('spotify:poll-now-playing')->assertSuccessful();

        Queue::assertNotPushed(FetchNowPlaying::class);
    });
});
