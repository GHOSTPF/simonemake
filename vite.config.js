import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Tailwind roda via PostCSS (postcss.config.js + tailwind.config.js),
// não pelo plugin @tailwindcss/vite — assim a paleta da marca fica num
// tailwind.config.js clássico, fácil de editar.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
