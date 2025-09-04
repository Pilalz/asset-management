import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/pages/location.js',
                'resources/js/pages/department.js',
                'resources/js/pages/assetClass.js',
                'resources/js/pages/assetSubClass.js',
                'resources/js/pages/assetName.js',
                'resources/js/pages/companyUser.js',
            ],
            refresh: true,
        }),
    ],
});