import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/tabler.css',
                'resources/js/tabler.js',
            ],
            refresh: true,
        }),
    ],
});
