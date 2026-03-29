import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/knowledge-base/theme.css',
                'resources/css/filament/streamer/theme.css',
                'resources/css/app.css',
                'resources/js/app.js',
                'app-modules/widget-player/resources/css/widget-player.css',
                'app-modules/widget-player/resources/js/widget-player.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
});
