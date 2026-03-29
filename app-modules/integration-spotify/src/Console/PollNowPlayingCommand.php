<?php

declare(strict_types=1);

namespace He4rt\IntegrationSpotify\Console;

use He4rt\Identity\ExternalIdentity\Enums\IdentityProvider;
use He4rt\Identity\ExternalIdentity\Models\ExternalIdentity;
use He4rt\IntegrationSpotify\Jobs\FetchNowPlaying;
use Illuminate\Console\Command;

class PollNowPlayingCommand extends Command
{
    protected $signature = 'spotify:poll-now-playing';

    protected $description = 'Poll Spotify for currently playing tracks and broadcast changes';

    public function handle(): void
    {
        ExternalIdentity::query()
            ->where('provider', IdentityProvider::Spotify)
            ->whereNotNull('connected_at')
            ->whereNull('disconnected_at')
            ->each(fn (ExternalIdentity $identity) => dispatch(new FetchNowPlaying($identity)));
    }
}
