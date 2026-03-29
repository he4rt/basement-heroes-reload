<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Models;

use App\Models\BaseModel;
use App\Models\Concerns\BelongsToTeam;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\Identity\Teams\Team;
use He4rt\WidgetPlayer\Database\Factories\PlayerProfileFactory;
use He4rt\WidgetPlayer\Enums\AnimationType;
use He4rt\WidgetPlayer\Enums\CoverStyle;
use He4rt\WidgetPlayer\Enums\PlayerSkin;
use He4rt\WidgetPlayer\Enums\PlayerTheme;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $team_id
 * @property string|null $external_identity_id
 * @property string $name
 * @property bool $is_active
 * @property string $browser_source_token
 * @property PlayerSkin $player_skin
 * @property CoverStyle $cover_style
 * @property bool $cover_glow
 * @property bool $cover_blur
 * @property string|null $nothing_playing_cover_path
 * @property bool $magic_colors
 * @property PlayerTheme $theme
 * @property string|null $tint_color
 * @property AnimationType $reveal_animation
 * @property AnimationType $exit_animation
 * @property string $font_family
 * @property string $nothing_playing_title
 * @property string $nothing_playing_artist
 * @property bool $hide_on_pause
 * @property int $hide_delay_seconds
 * @property bool $song_change_only
 * @property int $visible_duration_seconds
 * @property bool $hide_visualizer
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Team $team
 * @property-read ExternalIdentity|null $externalIdentity
 *
 * @extends BaseModel<PlayerProfileFactory>
 */
class PlayerProfile extends BaseModel
{
    use BelongsToTeam;
    use SoftDeletes;

    protected $table = 'widget_player_profiles';

    /**
     * @return BelongsTo<ExternalIdentity, $this>
     */
    public function externalIdentity(): BelongsTo
    {
        return $this->belongsTo(ExternalIdentity::class);
    }

    protected static function newFactory(): PlayerProfileFactory
    {
        return PlayerProfileFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'player_skin' => PlayerSkin::class,
            'cover_style' => CoverStyle::class,
            'cover_glow' => 'boolean',
            'cover_blur' => 'boolean',
            'magic_colors' => 'boolean',
            'theme' => PlayerTheme::class,
            'reveal_animation' => AnimationType::class,
            'exit_animation' => AnimationType::class,
            'hide_on_pause' => 'boolean',
            'hide_delay_seconds' => 'integer',
            'song_change_only' => 'boolean',
            'visible_duration_seconds' => 'integer',
            'hide_visualizer' => 'boolean',
        ];
    }
}
