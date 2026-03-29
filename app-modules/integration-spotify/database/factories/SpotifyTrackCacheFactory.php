<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Database\Factories;

use He4rt\IntegrationSpotify\Models\SpotifyTrackCache;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpotifyTrackCache>
 */
class SpotifyTrackCacheFactory extends Factory
{
    protected $model = SpotifyTrackCache::class;

    public function definition(): array
    {
        return [
            'track_id' => fake()->uuid(),
            'track_name' => fake()->sentence(3),
            'artist_name' => fake()->name(),
            'album_name' => fake()->sentence(2),
            'album_image_url' => fake()->imageUrl(640, 640),
            'duration_ms' => fake()->numberBetween(120000, 360000),
            'progress_ms' => fake()->numberBetween(0, 120000),
            'is_playing' => fake()->boolean(80),
        ];
    }

    public function notPlaying(): static
    {
        return $this->state([
            'is_playing' => false,
            'track_id' => null,
            'track_name' => null,
            'artist_name' => null,
            'album_name' => null,
            'album_image_url' => null,
            'duration_ms' => null,
            'progress_ms' => null,
        ]);
    }
}
