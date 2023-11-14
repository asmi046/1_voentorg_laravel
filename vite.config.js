import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'public/fonts/shop/style.css',
                'public/scss/style.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
