// JIMMY FISH AUTHORIZED ONLY

var gulp = require('gulp'),
    autoprefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    minifyCSS = require('gulp-minify-css'),
    map = require('gulp-sourcemaps'),
    concat = require('gulp-concat'),
    runSequence = require('run-sequence'),
    imageMin = require('gulp-imagemin');

var assetRawDir = './src/OfficeBundle/Resources/public/',
    compiledDir = './web/';

gulp.task('default', function() {
    runSequence(
        'script',
        'style',
        'vendorScript',
        'vendorStyle',
        'demoScript',
        'compress'
    );
});

gulp.task('script', function() {
    gulp.src(assetRawDir + 'js/**/*.js')
        .pipe(concat('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(compiledDir + 'js/'))
});

gulp.task('style', function() {
    gulp.src(assetRawDir + 'css/**/*.css')
        .pipe(concat('app.min.css'))
        .pipe(autoprefixer())
        .pipe(minifyCSS({
            keepSpecialComments: 0
        }))
        .pipe(gulp.dest(compiledDir + 'css/'))
});

gulp.task('vendorScript', function() {
    gulp.src([
            assetRawDir + 'js/vendor.min.js',
            assetRawDir + 'js/sweetalert2.min.js'
            // assetRawDir + 'vendors/bower_components/jquery/dist/jquery.min.js',
            // assetRawDir + 'vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
            // assetRawDir + 'vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
            // assetRawDir + 'vendors/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js',
            // assetRawDir + 'vendors/bower_components/moment/min/moment.min.js',
            // assetRawDir + 'vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            // assetRawDir + 'vendors/bower_components/fullcalendar/dist/fullcalendar.min.js',
            // assetRawDir + 'vendors/bower_components/simpleWeather/jquery.simpleWeather.min.js',
            // assetRawDir + 'vendors/bower_components/salvattore/dist/salvattore.min.js',
            // assetRawDir + 'vendors/bower_components/flot/jquery.flot.js',
            // assetRawDir + 'vendors/bower_components/flot/jquery.flot.resize.js',
            // assetRawDir + 'vendors/bower_components/flot.curvedlines/curvedLines.js',
            // assetRawDir + 'vendors/jquery.sparkline/jquery.sparkline.min.js',
            // assetRawDir + 'vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js',
            // assetRawDir + 'vendors/bower_components/jquery.bootgrid/dist/jquery.bootgrid.js',
            // assetRawDir + 'vendors/bower_components/select2/dist/js/select2.full.min.js',
            // assetRawDir + 'vendors/fileinput/fileinput.min.js'
        ])
        .pipe(concat('vendor.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(compiledDir + 'js/'))
});

gulp.task('vendorStyle', function() {
    gulp.src([
            assetRawDir + 'css/vendor.min.css',
            assetRawDir + 'css/sweetalert2.min.css'
        ])
        .pipe(concat('vendor.min.css'))
        .pipe(autoprefixer())
        .pipe(minifyCSS({
            keepSpecialComments: 0
        }))
        .pipe(gulp.dest(compiledDir + 'css/'))
});

gulp.task('demoScript', function() {
    gulp.src(assetRawDir + 'demo/**/*.js')
        .pipe(concat('demo.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(compiledDir + 'js/'))
});

gulp.task('cdnStyle', function() {
    gulp.src([
            // assetRawDir + 'css/jquery.dataTables.min.css',
            assetRawDir + 'css/bootstrapValidator.min.css',
            assetRawDir + 'css/bootstrap-datepicker.min.css'
        ])
        .pipe(concat('cdn.min.css'))
        .pipe(autoprefixer())
        .pipe(minifyCSS({
            keepSpecialComments: 0
        }))
        .pipe(gulp.dest(compiledDir + 'css/'))
});

gulp.task('cdnScript', function() {
    gulp.src([
            assetRawDir + 'js/bootstrap-datepicker.min.js',
            assetRawDir + 'js/moment.min.js',
            assetRawDir + 'js/bootstrapvalidator.min.js'
            // assetRawDir + 'js/jquery.dataTables.min.js'
        ])
        .pipe(concat('cdn.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(compiledDir + 'js/'))
});

gulp.task('compress', function() {
    gulp.src([
            assetRawDir + 'img/**/*.jpg',
            assetRawDir + 'img/**/*.png'
        ])
        .pipe(imageMin())
        .pipe(gulp.dest(compiledDir + 'images/'))
});
