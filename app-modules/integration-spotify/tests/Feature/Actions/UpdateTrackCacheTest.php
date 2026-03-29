<?php

declare(strict_types=1);

use He4rt\Identity\ExternalIdentity\Data\ClientAccessManager;
use He4rt\Identity\ExternalIdentity\Enums\CredentialsType;
use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use He4rt\Identity\ExternalIdentity\Enums\IdentityType;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\Identity\Teams\Team;
use He4rt\Identity\Users\User;
use He4rt\IntegrationSpotify\Actions\UpdateTrackCache;
use He4rt\IntegrationSpotify\Data\CurrentlyPlayingData;
use He4rt\IntegrationSpotify\Events\NowPlayingUpdated;
use He4rt\IntegrationSpotify\Models\SpotifyTrackCache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;

describe('UpdateTrackCache action', function (): void {
    beforeEach(function (): void {
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

        $this->action = resolve(UpdateTrackCache::class);
    });

    test('creates cache on first track data', function (): void {
        Event::fake([NowPlayingUpdated::class]);

        $data = new CurrentlyPlayingData(
            trackId: 'track123',
            trackName: 'Test Song',
            artistName: 'Test Artist',
            albumName: 'Test Album',
            albumImageUrl: 'https://example.com/image.jpg',
            durationMs: 200000,
            progressMs: 30000,
            isPlaying: true,
        );

        $result = $this->action->handle($this->identity, $data);

        expect($result)->toBeTrue();

        $cache = SpotifyTrackCache::query()->firstWhere('external_identity_id', $this->identity->id);
        expect($cache)
            ->not->toBeNull()
            ->track_id->toBe('track123')
            ->track_name->toBe('Test Song')
            ->is_playing->toBeTrue();
    });

    test('updates cache when track changes', function (): void {
        Event::fake([NowPlayingUpdated::class]);

        SpotifyTrackCache::query()->create([
            'external_identity_id' => $this->identity->id,
            'track_id' => 'old-track',
            'track_name' => 'Old Song',
            'artist_name' => 'Old Artist',
            'album_name' => 'Old Album',
            'is_playing' => true,
        ]);

        $data = new CurrentlyPlayingData(
            trackId: 'new-track',
            trackName: 'New Song',
            artistName: 'New Artist',
            albumName: 'New Album',
            albumImageUrl: 'https://example.com/new.jpg',
            durationMs: 180000,
            progressMs: 10000,
            isPlaying: true,
        );

        $result = $this->action->handle($this->identity, $data);

        expect($result)->toBeTrue();

        $cache = SpotifyTrackCache::query()->firstWhere('external_identity_id', $this->identity->id);
        expect($cache->track_id)->toBe('new-track');
        expect($cache->track_name)->toBe('New Song');
    });

    test('returns false when track has not changed', function (): void {
        Event::fake([NowPlayingUpdated::class]);

        SpotifyTrackCache::query()->create([
            'external_identity_id' => $this->identity->id,
            'track_id' => 'same-track',
            'track_name' => 'Same Song',
            'artist_name' => 'Same Artist',
            'album_name' => 'Same Album',
            'is_playing' => true,
        ]);

        $data = new CurrentlyPlayingData(
            trackId: 'same-track',
            trackName: 'Same Song',
            artistName: 'Same Artist',
            albumName: 'Same Album',
            albumImageUrl: null,
            durationMs: null,
            progressMs: null,
            isPlaying: true,
        );

        $result = $this->action->handle($this->identity, $data);

        expect($result)->toBeFalse();
        Event::assertNotDispatched(NowPlayingUpdated::class);
    });

    test('detects is_playing state change', function (): void {
        Event::fake([NowPlayingUpdated::class]);

        SpotifyTrackCache::query()->create([
            'external_identity_id' => $this->identity->id,
            'track_id' => 'same-track',
            'track_name' => 'Same Song',
            'artist_name' => 'Same Artist',
            'album_name' => 'Same Album',
            'is_playing' => true,
        ]);

        $data = new CurrentlyPlayingData(
            trackId: 'same-track',
            trackName: 'Same Song',
            artistName: 'Same Artist',
            albumName: 'Same Album',
            albumImageUrl: null,
            durationMs: null,
            progressMs: null,
            isPlaying: false,
        );

        $result = $this->action->handle($this->identity, $data);

        expect($result)->toBeTrue();

        $cache = SpotifyTrackCache::query()->firstWhere('external_identity_id', $this->identity->id);
        expect($cache->is_playing)->toBeFalse();
    });
});
