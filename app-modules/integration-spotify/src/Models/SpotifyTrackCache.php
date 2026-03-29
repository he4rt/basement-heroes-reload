<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Models;

use App\Models\BaseModel;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\IntegrationSpotify\Database\Factories\SpotifyTrackCacheFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @template TFactory of SpotifyTrackCacheFactory
 *
 * @property string $id
 * @property string $external_identity_id
 * @property ?string $track_id
 * @property ?string $track_name
 * @property ?string $artist_name
 * @property ?string $album_name
 * @property ?string $album_image_url
 * @property ?int $duration_ms
 * @property ?int $progress_ms
 * @property bool $is_playing
 */
class SpotifyTrackCache extends BaseModel
{
    protected $table = 'spotify_track_cache';

    /**
     * @return BelongsTo<ExternalIdentity, $this>
     */
    public function externalIdentity(): BelongsTo
    {
        return $this->belongsTo(ExternalIdentity::class);
    }

    protected function casts(): array
    {
        return [
            'duration_ms' => 'integer',
            'progress_ms' => 'integer',
            'is_playing' => 'boolean',
        ];
    }
}
