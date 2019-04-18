let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/index.scss', 'public/css')
    .version();


mix.js('resources/assets/js/common_pc/payment.js', 'public/payment/payment_pc.js');
// .extract('vue');

mix.js('resources/assets/js/common_mobile/payment.js', 'public/payment/payment_mobile.js');


