import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    alpine: ['alpinejs'],
                    axios: ['axios'],
                    quill: ['quill'],
                },
            },
        },
        chunkSizeWarningLimit: 600,
    },
});
