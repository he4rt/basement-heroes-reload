<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Actions;

use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\IntegrationSpotify\Data\CurrentlyPlayingData;
use He4rt\IntegrationSpotify\Events\NowPlayingUpdated;
use He4rt\IntegrationSpotify\Models\SpotifyTrackCache;
use He4rt\WidgetPlayer\Models\PlayerProfile;
use Illuminate\Support\Facades\Cache;

class UpdateTrackCache
{
    public function handle(ExternalIdentity $identity, CurrentlyPlayingData $data): bool
    {
        $cache = SpotifyTrackCache::query()->firstWhere('external_identity_id', $identity->id);

        if (!$this->hasChanged($cache, $data)) {
            return false;
        }

        SpotifyTrackCache::query()->updateOrCreate(['external_identity_id' => $identity->id], [
            'track_id' => $data->trackId,
            'track_name' => $data->trackName,
            'artist_name' => $data->artistName,
            'album_name' => $data->albumName,
            'album_image_url' => $data->albumImageUrl,
            'duration_ms' => $data->durationMs,
            'progress_ms' => $data->progressMs,
            'is_playing' => $data->isPlaying,
        ]);

        $this->broadcast($identity, $data);

        return true;
    }

    private function hasChanged(?SpotifyTrackCache $cache, CurrentlyPlayingData $data): bool
    {
        if (!$cache instanceof SpotifyTrackCache) {
            return true;
        }

        return $cache->track_id !== $data->trackId
            || $cache->is_playing !== $data->isPlaying;
    }

    private function broadcast(ExternalIdentity $identity, CurrentlyPlayingData $data): void
    {
        $cacheKey = 'spotify.player_profile.'.$identity->id;

        $playerProfile = Cache::flexible($cacheKey, [30, 60], fn () => PlayerProfile::query()
            ->where('team_id', $identity->team_id)
            ->where('external_identity_id', $identity->id)
            ->first());

        if ($playerProfile?->browser_source_token) {
            broadcast(new NowPlayingUpdated(
                browserSourceToken: $playerProfile->browser_source_token,
                data: $data,
            ));
        }
    }
}
