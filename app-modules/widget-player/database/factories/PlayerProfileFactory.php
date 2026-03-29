<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Database\Factories;

use He4rt\Identity\Teams\Team;
use He4rt\WidgetPlayer\Enums\AnimationType;
use He4rt\WidgetPlayer\Enums\CoverStyle;
use He4rt\WidgetPlayer\Enums\PlayerSkin;
use He4rt\WidgetPlayer\Enums\PlayerTheme;
use He4rt\WidgetPlayer\Models\PlayerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PlayerProfile>
 */
class PlayerProfileFactory extends Factory
{
    protected $model = PlayerProfile::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => 'Main',
            'is_active' => true,
            'browser_source_token' => Str::random(32),
            'player_skin' => PlayerSkin::Compact,
            'cover_style' => CoverStyle::Square,
            'cover_glow' => false,
            'cover_blur' => false,
            'magic_colors' => false,
            'theme' => PlayerTheme::Dark,
            'reveal_animation' => AnimationType::Original,
            'exit_animation' => AnimationType::Original,
            'font_family' => 'Poppins',
            'nothing_playing_title' => 'Nothing Playing',
            'nothing_playing_artist' => 'Get the music started',
            'hide_on_pause' => false,
            'hide_delay_seconds' => 5,
            'song_change_only' => false,
            'visible_duration_seconds' => 5,
            'hide_visualizer' => false,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function withSkin(PlayerSkin $skin): static
    {
        return $this->state(fn () => [
            'player_skin' => $skin,
        ]);
    }
}
