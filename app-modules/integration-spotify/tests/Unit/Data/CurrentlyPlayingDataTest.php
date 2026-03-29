<?php

declare(strict_types=1);

use He4rt\IntegrationSpotify\Data\CurrentlyPlayingData;

describe('CurrentlyPlayingData', function (): void {
    test('fromApiResponse maps Spotify API response correctly', function (): void {
        $apiResponse = [
            'is_playing' => true,
            'progress_ms' => 45000,
            'item' => [
                'id' => 'track123',
                'name' => 'Bohemian Rhapsody',
                'duration_ms' => 354000,
                'artists' => [
                    ['name' => 'Queen'],
                ],
                'album' => [
                    'name' => 'A Night at the Opera',
                    'images' => [
                        ['url' => 'https://i.scdn.co/image/large', 'height' => 640, 'width' => 640],
                        ['url' => 'https://i.scdn.co/image/medium', 'height' => 300, 'width' => 300],
                    ],
                ],
            ],
        ];

        $data = CurrentlyPlayingData::fromApiResponse($apiResponse);

        expect($data)
            ->trackId->toBe('track123')
            ->trackName->toBe('Bohemian Rhapsody')
            ->artistName->toBe('Queen')
            ->albumName->toBe('A Night at the Opera')
            ->albumImageUrl->toBe('https://i.scdn.co/image/large')
            ->durationMs->toBe(354000)
            ->progressMs->toBe(45000)
            ->isPlaying->toBeTrue();
    });

    test('fromApiResponse joins multiple artist names', function (): void {
        $apiResponse = [
            'is_playing' => true,
            'progress_ms' => 0,
            'item' => [
                'id' => 'track456',
                'name' => 'Under Pressure',
                'duration_ms' => 248000,
                'artists' => [
                    ['name' => 'Queen'],
                    ['name' => 'David Bowie'],
                ],
                'album' => [
                    'name' => 'Hot Space',
                    'images' => [
                        ['url' => 'https://i.scdn.co/image/album', 'height' => 640, 'width' => 640],
                    ],
                ],
            ],
        ];

        $data = CurrentlyPlayingData::fromApiResponse($apiResponse);

        expect($data->artistName)->toBe('Queen, David Bowie');
    });

    test('fromApiResponse handles null item gracefully', function (): void {
        $apiResponse = [
            'is_playing' => false,
            'progress_ms' => null,
            'item' => null,
        ];

        $data = CurrentlyPlayingData::fromApiResponse($apiResponse);

        expect($data)
            ->trackId->toBeNull()
            ->trackName->toBeNull()
            ->artistName->toBeNull()
            ->albumName->toBeNull()
            ->albumImageUrl->toBeNull()
            ->durationMs->toBeNull()
            ->progressMs->toBeNull()
            ->isPlaying->toBeFalse();
    });

    test('empty returns data with all nulls and isPlaying false', function (): void {
        $data = CurrentlyPlayingData::empty();

        expect($data)
            ->trackId->toBeNull()
            ->trackName->toBeNull()
            ->artistName->toBeNull()
            ->albumName->toBeNull()
            ->albumImageUrl->toBeNull()
            ->durationMs->toBeNull()
            ->progressMs->toBeNull()
            ->isPlaying->toBeFalse();
    });
});
