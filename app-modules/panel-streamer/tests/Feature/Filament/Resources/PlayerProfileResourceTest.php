<?php

declare(strict_types=1);

namespace He4rt\Streamer\Tests\Feature\Filament\Resources;

use Filament\Facades\Filament;
use He4rt\Identity\Teams\Team;
use He4rt\Identity\Users\User;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages\CreatePlayerProfile;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages\EditPlayerProfile;
use He4rt\Streamer\Filament\Resources\PlayerProfiles\Pages\ListPlayerProfiles;
use He4rt\WidgetPlayer\Enums\AnimationType;
use He4rt\WidgetPlayer\Enums\CoverStyle;
use He4rt\WidgetPlayer\Enums\PlayerSkin;
use He4rt\WidgetPlayer\Enums\PlayerTheme;
use He4rt\WidgetPlayer\Models\PlayerProfile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['owner_id' => $this->user->id]);
    $this->user->teams()->attach($this->team);

    actingAs($this->user);

    $panel = Filament::getPanel('streamer');
    Filament::setCurrentPanel($panel);
    Filament::setTenant($this->team);
    $panel->boot();
});

it('can list player profiles for the current team', function (): void {
    $profiles = PlayerProfile::factory()
        ->count(3)
        ->create(['team_id' => $this->team->id]);

    livewire(ListPlayerProfiles::class)
        ->loadTable()
        ->assertCanSeeTableRecords($profiles)
        ->assertCountTableRecords(3);
});

it('cannot see player profiles from another team', function (): void {})->skip('Filament tenant scoping requires full HTTP lifecycle');

it('can create a player profile', function (): void {
    livewire(CreatePlayerProfile::class)
        ->fillForm([
            'name' => 'My Stream Player',
            'is_active' => true,
            'player_skin' => PlayerSkin::Compact->value,
            'theme' => PlayerTheme::Dark->value,
            'cover_style' => CoverStyle::Square->value,
            'cover_glow' => false,
            'cover_blur' => false,
            'magic_colors' => true,
            'font_family' => 'Poppins',
            'nothing_playing_title' => 'No song playing',
            'nothing_playing_artist' => 'Get the music started',
            'reveal_animation' => AnimationType::Original->value,
            'exit_animation' => AnimationType::Original->value,
            'hide_on_pause' => false,
            'hide_delay_seconds' => 0,
            'song_change_only' => false,
            'visible_duration_seconds' => 10,
            'hide_visualizer' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(PlayerProfile::class, [
        'name' => 'My Stream Player',
        'team_id' => $this->team->id,
        'is_active' => true,
    ]);

    $profile = PlayerProfile::query()->where('name', 'My Stream Player')->first();
    expect($profile->browser_source_token)->not->toBeNull()
        ->and($profile->browser_source_token)->toHaveLength(32);
});

it('validates name is required on create', function (): void {
    livewire(CreatePlayerProfile::class)
        ->fillForm([
            'name' => null,
            'player_skin' => PlayerSkin::Compact->value,
            'theme' => PlayerTheme::Dark->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('can update a player profile', function (): void {
    $profile = PlayerProfile::factory()->create(['team_id' => $this->team->id]);

    livewire(EditPlayerProfile::class, [
        'record' => $profile->getRouteKey(),
    ])
        ->fillForm([
            'name' => 'Updated Player Name',
            'player_skin' => PlayerSkin::Discord->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($profile->refresh())
        ->name->toBe('Updated Player Name')
        ->player_skin->toBe(PlayerSkin::Discord);
});
