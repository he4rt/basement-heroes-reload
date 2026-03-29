import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const config = window.__WIDGET_CONFIG__;
const body = document.body;

// DOM elements
const player = document.getElementById('player');
const coverImg = document.getElementById('cover');
const trackName = document.getElementById('track-name');
const artistName = document.getElementById('artist-name');

// Set font
if (body.dataset.fontFamily) {
    player.style.fontFamily = `'${body.dataset.fontFamily}', sans-serif`;
}

// Initialize Echo with server-injected Reverb config
const echo = new Echo({
    broadcaster: 'reverb',
    key: config.reverb.key,
    wsHost: config.reverb.host,
    wsPort: config.reverb.port,
    wssPort: config.reverb.port,
    forceTLS: config.reverb.scheme === 'https',
    enabledTransports: ['ws', 'wss'],
});

let currentTrackId = null;
let hideTimeout = null;

function updatePlayer(data) {
    if (!data.is_playing) {
        if (body.dataset.hideOnPause === 'true') {
            scheduleHide();
        } else {
            showIdle();
        }
        return;
    }

    clearHideTimeout();

    const trackChanged = data.track_id !== currentTrackId;
    currentTrackId = data.track_id;

    if (trackChanged) {
        // Fade out, update, fade in
        player.classList.add('player--transitioning');
        setTimeout(() => {
            coverImg.src = data.album_image_url || '';
            trackName.textContent = data.track_name || '';
            artistName.textContent = data.artist_name || '';
            player.classList.remove('player--hidden', 'player--transitioning');
        }, 300);
    } else {
        player.classList.remove('player--hidden');
    }

    // Song change only mode: hide after visible_duration
    if (trackChanged && body.dataset.songChangeOnly === 'true') {
        const duration = parseInt(body.dataset.visibleDuration, 10) || 5;
        hideTimeout = setTimeout(() => {
            player.classList.add('player--hidden');
        }, duration * 1000);
    }
}

function showIdle() {
    player.classList.add('player--transitioning');
    setTimeout(() => {
        coverImg.src = '';
        trackName.textContent = body.dataset.nothingPlayingTitle || 'Nothing Playing';
        artistName.textContent = body.dataset.nothingPlayingArtist || '';
        player.classList.remove('player--transitioning');
        currentTrackId = null;
    }, 300);
}

function scheduleHide() {
    const delay = parseInt(body.dataset.hideDelay, 10) || 5;
    clearHideTimeout();
    hideTimeout = setTimeout(() => {
        player.classList.add('player--hidden');
        currentTrackId = null;
    }, delay * 1000);
}

function clearHideTimeout() {
    if (hideTimeout) {
        clearTimeout(hideTimeout);
        hideTimeout = null;
    }
}

// Set initial state
if (config.currentTrack && config.currentTrack.is_playing) {
    coverImg.src = config.currentTrack.album_image_url || '';
    trackName.textContent = config.currentTrack.track_name || '';
    artistName.textContent = config.currentTrack.artist_name || '';
    currentTrackId = config.currentTrack.track_id;
} else {
    trackName.textContent = body.dataset.nothingPlayingTitle || 'Nothing Playing';
    artistName.textContent = body.dataset.nothingPlayingArtist || '';
}

// Subscribe to channel — note leading dot for custom broadcastAs()
echo.channel(`spotify.now-playing.${config.token}`).listen('.now-playing.updated', (data) => {
    updatePlayer(data);
});
