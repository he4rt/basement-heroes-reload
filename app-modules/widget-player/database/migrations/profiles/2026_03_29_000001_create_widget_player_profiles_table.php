<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('widget_player_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained('identity_teams')->cascadeOnDelete();
            $table->foreignUuid('external_identity_id')->nullable()->constrained('identity_external_identities')->nullOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('browser_source_token')->unique();

            // Appearance
            $table->string('player_skin')->default('compact');
            $table->string('cover_style')->default('square');
            $table->boolean('cover_glow')->default(false);
            $table->boolean('cover_blur')->default(false);
            $table->string('nothing_playing_cover_path')->nullable();

            // Colors
            $table->boolean('magic_colors')->default(false);
            $table->string('theme')->default('dark');
            $table->string('tint_color', 7)->nullable();

            // Animations
            $table->string('reveal_animation')->default('original');
            $table->string('exit_animation')->default('original');

            // Font
            $table->string('font_family')->default('Poppins');

            // Text
            $table->string('nothing_playing_title')->default('Nothing Playing');
            $table->string('nothing_playing_artist')->default('Get the music started');

            // Visibility
            $table->boolean('hide_on_pause')->default(false);
            $table->unsignedSmallInteger('hide_delay_seconds')->default(5);
            $table->boolean('song_change_only')->default(false);
            $table->unsignedSmallInteger('visible_duration_seconds')->default(5);

            // Misc
            $table->boolean('hide_visualizer')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widget_player_profiles');
    }
};
