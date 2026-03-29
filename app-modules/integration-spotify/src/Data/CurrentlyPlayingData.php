<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Data;

class CurrentlyPlayingData
{
    public function __construct(
        public readonly ?string $trackId,
        public readonly ?string $trackName,
        public readonly ?string $artistName,
        public readonly ?string $albumName,
        public readonly ?string $albumImageUrl,
        public readonly ?int $durationMs,
        public readonly ?int $progressMs,
        public readonly bool $isPlaying,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiResponse(array $data): self
    {
        $item = $data['item'] ?? null;

        return new self(
            trackId: $item['id'] ?? null,
            trackName: $item['name'] ?? null,
            artistName: isset($item['artists'])
                ? collect($item['artists'])->pluck('name')->join(', ')
                : null,
            albumName: $item['album']['name'] ?? null,
            albumImageUrl: $item['album']['images'][0]['url'] ?? null,
            durationMs: $item['duration_ms'] ?? null,
            progressMs: $data['progress_ms'] ?? null,
            isPlaying: $data['is_playing'] ?? false,
        );
    }

    public static function empty(): self
    {
        return new self(
            trackId: null,
            trackName: null,
            artistName: null,
            albumName: null,
            albumImageUrl: null,
            durationMs: null,
            progressMs: null,
            isPlaying: false,
        );
    }
}
