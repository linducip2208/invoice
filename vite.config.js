import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/tabler.css',
                'resources/js/tabler.js',
            ],
            refresh: true,
        }),
    ],
});
