const {mix} = require('laravel-mix');

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

mix
    .js('resources/assets/js/app.js', 'public/js')
    .scripts([
        'resources/assets/jquery/jquery-2.2.4.min.js',
        'bower_components/jquery-ui/jquery-ui.min.js',
        'bower_components/moment/min/moment.min.js',
        'bower_componentsootstrap/dist/js/bootstrap.js',
        'bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        'bower_components/jquery-validation/dist/jquery.validate.js',
        'bower_components/jquery-validation/dist/additional-methods.js',
        'resources/assets/bootstrap-daterangepicker/daterangepicker.js',
        'resources/assets/geocomplete/jquery.geocomplete.min.js',
        'resources/assets/cropper/cropper.js',
        'resources/assets/settingsjs/settingsjs.js',
        'resources/assets/js/bootstrap-select.js',
        'resources/assets/select2/js/select2.min.js',
        'resources/assets/multiselect/jquery.multi-select.js',
        'resources/assets/js/jQuery.print.js',
        'resources/assets/js/card.js',
        'resources/assets/intel-input/js/intlTelInput.min.js',
        'resources/assets/js/timeout-dialog.js',
        'resources/assets/toastr/toastr.min.js',
        'resources/assets/vanillatoast/vanillatoasts.js',
        'resources/assets/bootstrap3-editable/js/bootstrap-editable.min.js',
        'resources/assets/tipped/tipped.js',
        'resources/assets/fancybox/jquery.fancybox.min.js',
        'resources/assets/introjs/intro.min.js',
        'bower_components/fullcalendar/dist/fullcalendar.min.js',
        'resources/assets/owlcarousel/owl.carousel.min.js'
    ], 'public/assets/js/app.js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .styles([
        'bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
        'resources/assets/bootstrap-daterangepicker/daterangepicker.css',
        'resources/assets/css/style.css',
        'resources/assets/css/docs.css',
        'resources/assets/css/custom.css',
        'resources/assets/cropper/cropper.min.css',
        'resources/assets/settingsjs/settingsjs.css',
        'resources/assets/select2/css/select2.min.css',
        'resources/assets/select2/css/bootstrap-select2.css',
        'resources/assets/multiselect/multi-select.css',
        'resources/assets/css/offline-theme-default.css',
        'resources/assets/intel-input/css/intlTelInput.css',
        'resources/assets/css/timeout-dialog.css',
        'bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css',
        'resources/assets/toastr/toastr.min.css',
        'resources/assets/vanillatoast/vanillatoasts.css',
        'resources/assets/bootstrap3-editable/css/bootstrap-editable.css',
        'resources/assets/tipped/tipped.css',
        'resources/assets/fancybox/jquery.fancybox.min.css',
        'resources/assets/introjs/introjs.min.css',
        'bower_components/fullcalendar/dist/fullcalendar.min.css',
        'resources/assets/css/animate.css'
    ], 'public/css/all.css').version();
mix.browserSync('partypeoplecore.dev');
if (mix.inProduction()) {
    mix.version();
}
