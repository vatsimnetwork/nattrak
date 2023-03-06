import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/site.scss',
                'resources/css/datatables.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
