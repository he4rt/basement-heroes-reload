<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Now Playing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family={{ urlencode($profile->font_family) }}:wght@400;600&display=swap"
        rel="stylesheet"
    />
    @vite ([
        'app-modules/widget-player/resources/css/widget-player.css',
        'app-modules/widget-player/resources/js/widget-player.js'
    ])
</head>
<body
    data-theme="{{ $profile->theme->value }}"
    data-font-family="{{ $profile->font_family }}"
    data-hide-on-pause="{{ $profile->hide_on_pause ? 'true' : 'false' }}"
    data-hide-delay="{{ $profile->hide_delay_seconds }}"
    data-song-change-only="{{ $profile->song_change_only ? 'true' : 'false' }}"
    data-visible-duration="{{ $profile->visible_duration_seconds }}"
    data-nothing-playing-title="{{ $profile->nothing_playing_title }}"
    data-nothing-playing-artist="{{ $profile->nothing_playing_artist }}"
>
    <div id="player" class="player">
        <img id="cover" class="player__cover" src="{{ $currentTrack?->album_image_url ?? '' }}" alt="" />
        <div class="player__info">
            <div id="track-name" class="player__track">
                {{
                    $currentTrack?->is_playing
                        ? $currentTrack->track_name
                        : $profile->nothing_playing_title
                }}
            </div>
            <div id="artist-name" class="player__artist">
                {{
                    $currentTrack?->is_playing
                        ? $currentTrack->artist_name
                        : $profile->nothing_playing_artist
                }}
            </div>
        </div>
    </div>

    <script>
        window.__WIDGET_CONFIG__ = {
            token: @json ($profile->browser_source_token),
            reverb: {
                key: @json (config('broadcasting.connections.reverb.key')),
                host: @json (config('broadcasting.connections.reverb.host')),
                port: @json (config('broadcasting.connections.reverb.port')),
                scheme: @json (config('broadcasting.connections.reverb.scheme', 'https')),
            },
            currentTrack: @json ($currentTrack
                ? [
                    'track_id' => $currentTrack->track_id,
                    'track_name' => $currentTrack->track_name,
                    'artist_name' => $currentTrack->artist_name,
                    'album_name' => $currentTrack->album_name,
                    'album_image_url' => $currentTrack->album_image_url,
                    'duration_ms' => $currentTrack->duration_ms,
                    'progress_ms' => $currentTrack->progress_ms,
                    'is_playing' => $currentTrack->is_playing
                ]
                : null),
        };
    </script>
</body>
</html>
