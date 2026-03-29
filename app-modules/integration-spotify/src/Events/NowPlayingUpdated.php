<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Events;

use He4rt\IntegrationSpotify\Data\CurrentlyPlayingData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NowPlayingUpdated implements ShouldBroadcast
{
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public string $browserSourceToken,
        public CurrentlyPlayingData $data,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('spotify.now-playing.'.$this->browserSourceToken);
    }

    public function broadcastAs(): string
    {
        return 'now-playing.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'track_id' => $this->data->trackId,
            'track_name' => $this->data->trackName,
            'artist_name' => $this->data->artistName,
            'album_name' => $this->data->albumName,
            'album_image_url' => $this->data->albumImageUrl,
            'duration_ms' => $this->data->durationMs,
            'progress_ms' => $this->data->progressMs,
            'is_playing' => $this->data->isPlaying,
        ];
    }
}
