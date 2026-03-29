<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_track_cache', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('external_identity_id')
                ->unique()
                ->constrained('identity_external_identities')
                ->cascadeOnDelete();
            $table->string('track_id')->nullable();
            $table->string('track_name')->nullable();
            $table->string('artist_name')->nullable();
            $table->string('album_name')->nullable();
            $table->string('album_image_url')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->integer('progress_ms')->nullable();
            $table->boolean('is_playing')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_track_cache');
    }
};
