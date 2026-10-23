import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament/common/theme.css',
                'resources/js/app.js',
                'resources/js/iframe.js',
            ],
            assets: [
                'resources/images/**',
                'resources/geojson/**/*.geojson',
                // Exclude source files
                '!../geojson/ro_*_poligon.geojson',
            ],
            refresh: [...refreshPaths, 'app/Livewire/**'],
        }),
    ],
});
