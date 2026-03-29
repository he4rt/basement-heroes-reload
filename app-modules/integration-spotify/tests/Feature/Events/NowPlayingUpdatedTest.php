<?php

declare(strict_types=1);

use He4rt\IntegrationSpotify\Data\CurrentlyPlayingData;
use He4rt\IntegrationSpotify\Events\NowPlayingUpdated;
use Illuminate\Broadcasting\Channel;

describe('NowPlayingUpdated event', function (): void {
    test('broadcasts on correct channel with token', function (): void {
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

        $event = new NowPlayingUpdated(
            browserSourceToken: 'abc-123-token',
            data: $data,
        );

        $channel = $event->broadcastOn();

        expect($channel)
            ->toBeInstanceOf(Channel::class)
            ->and($channel->name)->toBe('spotify.now-playing.abc-123-token');
    });

    test('broadcasts with correct event name', function (): void {
        $data = CurrentlyPlayingData::empty();
        $event = new NowPlayingUpdated(browserSourceToken: 'token', data: $data);

        expect($event->broadcastAs())->toBe('now-playing.updated');
    });

    test('broadcastWith returns correct payload', function (): void {
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

        $event = new NowPlayingUpdated(browserSourceToken: 'token', data: $data);
        $payload = $event->broadcastWith();

        expect($payload)->toBe([
            'track_id' => 'track123',
            'track_name' => 'Test Song',
            'artist_name' => 'Test Artist',
            'album_name' => 'Test Album',
            'album_image_url' => 'https://example.com/image.jpg',
            'duration_ms' => 200000,
            'progress_ms' => 30000,
            'is_playing' => true,
        ]);
    });
});
