<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Jobs;

use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\IntegrationSpotify\Actions\GetCurrentlyPlaying;
use He4rt\IntegrationSpotify\Actions\UpdateTrackCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchNowPlaying implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 15;

    public function __construct(
        public ExternalIdentity $identity,
    ) {}

    public function handle(
        GetCurrentlyPlaying $getCurrentlyPlaying,
        UpdateTrackCache $updateTrackCache,
    ): void {
        $data = $getCurrentlyPlaying->handle($this->identity);

        $updateTrackCache->handle($this->identity, $data);
    }
}
