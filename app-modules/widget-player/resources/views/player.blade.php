<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Now Playing</title>
</head>
<body data-profile-id="{{ $profile->id }}"
      data-skin="{{ $profile->player_skin->value }}"
      data-theme="{{ $profile->theme->value }}"
      data-cover-style="{{ $profile->cover_style->value }}"
      data-reveal-animation="{{ $profile->reveal_animation->value }}"
      data-exit-animation="{{ $profile->exit_animation->value }}"
      data-cover-glow="{{ $profile->cover_glow ? 'true' : 'false' }}"
      data-cover-blur="{{ $profile->cover_blur ? 'true' : 'false' }}"
      data-magic-colors="{{ $profile->magic_colors ? 'true' : 'false' }}"
      data-hide-on-pause="{{ $profile->hide_on_pause ? 'true' : 'false' }}"
      data-hide-delay="{{ $profile->hide_delay_seconds }}"
      data-song-change-only="{{ $profile->song_change_only ? 'true' : 'false' }}"
      data-visible-duration="{{ $profile->visible_duration_seconds }}"
      data-hide-visualizer="{{ $profile->hide_visualizer ? 'true' : 'false' }}"
      data-font-family="{{ $profile->font_family }}"
      data-tint-color="{{ $profile->tint_color }}"
      data-nothing-playing-title="{{ $profile->nothing_playing_title }}"
      data-nothing-playing-artist="{{ $profile->nothing_playing_artist }}">
</body>
</html>
