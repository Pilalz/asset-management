import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                //Datatables
                'resources/js/pages/location.js',
                'resources/js/pages/department.js',
                'resources/js/pages/assetClass.js',
                'resources/js/pages/assetSubClass.js',
                'resources/js/pages/assetName.js',
                'resources/js/pages/companyUser.js',
                'resources/js/pages/registerAsset.js',
                'resources/js/pages/transferAsset.js',
                'resources/js/pages/disposalAsset.js',
                'resources/js/pages/commercialDepre.js',
                'resources/js/pages/company.js',
                'resources/js/pages/history.js',
                //Signature Pad User
                'resources/js/pages/profileSignature.js',
                //Alert
                'resources/js/pages/alert.js',
            ],
            refresh: true,
        }),
    ],
});