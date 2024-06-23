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
                'public/scss/cart/all-cart.scss',
                'resources/js/app.js',
                'resources/js/cart.js',
                'public/js/mobile_catalog_menu.js',
                'public/js/fslightbox.js',
            ],
            refresh: true,
        }),
    ],
});
