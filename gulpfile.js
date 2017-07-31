// Require all dev dependencies.
var gulp      = require('gulp'),
    minify    = require('gulp-minify'),
    watch     = require('gulp-watch'),
    cleanCSS  = require('gulp-clean-css'),
    rename    = require('gulp-rename'),
    postcss   = require('gulp-postcss'),
    sass      = require('gulp-sass'),
    zip       = require('gulp-zip'),
    autoprefixer = require('autoprefixer'),
    browserSync  = require('browser-sync').create(),
		sourcemaps   = require('gulp-sourcemaps'),
		imagemin 		 = require('gulp-imagemin');

// me.js contains vars to your specific setup
var me = require('./gulpconf.js');

var WEBSITE   = me.WEBSITE;
var CONTENT_TYPE = me.CONTENT_TYPE;
var BASE_NAME = __dirname.match(/([^\/]*)\/*$/)[1];

// JS source, destination, and excludes.
var JS_EXCLD  = '!assets/js/*.min.js',
    JS_SRC    = 'assets/js/*.js',
    JS_DEST   = 'assets/js/';

// CSS and SASS src, dest, and exclude.
var CSS_SRC   = 'assets/css/*.css',
		CSS_DEST  = 'assets/css/',
		CSS_EXCLD = '!assets/css/*.min.css',
		SASS_WATCH  = 'assets/scss/*.scss';
if( 'plugin' === CONTENT_TYPE){
		SASS_SRC  = 'assets/scss/*.scss';
}else{
		SASS_SRC  = ['assets/scss/*.scss', '!assets/scss/style.scss' ];
}

// Image src and dest.
var IMG_SRC  = 'assets/images/*',
		IMG_DEST = 'assets/images';

// Zip src and options.
var ZIP_SRC_ARR = [
  './**',
  '!./composer.*',
  '!./gulpfile.js',
  '!./package.json',
  '!./README.md',
  '!./phpcs.xml',
  '!./phpunit.xml.dist',
  '!./{node_modules,node_modules/**}',
  '!./{bin,bin/**}',
  '!./{dist,dist/**}',
  '!./{vendor,vendor/**}',
  '!./{tests,tests/**}'
];
var ZIP_OPTS = { base: '..' };

// PHP Source.
var PHP_SRC = '**/*.php';

/*******************************************************************************
 *                                Gulp Tasks
 ******************************************************************************/

/**
 * Default gulp task. Initializes browserSync proxy server and watches src files
 * for any changes.
 *
 * CMD: gulp
 */
gulp.task('default', function() {

  browserSync.init({
    proxy: WEBSITE
  });

  gulp.watch( SASS_SRC, ['build-sass']);
  gulp.watch( JS_SRC , ['js-watch']);
  gulp.watch( PHP_SRC, function(){
    browserSync.reload();
  });
});

/**
 * JS Watch task. This is a dependency task for the default gulp task that
 * builds the js files and reloads the browser in the correct order
 *
 * CMD: None. Not meant to be run as standalone command.
 */
gulp.task('js-watch', ['build-js'], function(){
  browserSync.reload();
});

/**
 * Compiles SCSS into regular CSS.
 *
 * CMD: gulp build-sass
 */
gulp.task('build-sass', function() {
  gulp.src( SASS_SRC )
		.pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
		.pipe(postcss([
      autoprefixer({browsers: ['> 5% in US']})
    ]))
    .pipe(cleanCSS({compatibility: 'ie8'}))
		.pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(CSS_DEST));

	gulp.src( 'assets/scss/style.scss' )
		.pipe(sourcemaps.init())
	  .pipe(sass().on('error', sass.logError))
		.pipe(postcss([
      autoprefixer({browsers: ['> 5% in US']})
    ]))
    .pipe(cleanCSS({compatibility: 'ie8'}))
		.pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('.'))
    .pipe(browserSync.stream());
});

/**
 * Minifies JS files.
 *
 * CMD: gulp build-js
 */
gulp.task('build-js', function(){
  gulp.src( [ JS_SRC, JS_EXCLD ] )
    .pipe(minify({
      ext:{
        src:'.js',
        min:'.min.js'
      },
      noSource: true
    }))
    .pipe(gulp.dest( JS_DEST ));
});

gulp.task('build-img', function(){
	gulp.src('assets/images/*')
    .pipe(imagemin())
    .pipe(gulp.dest('assets/images'));
});

/**
 * Executes all of the build tasks in the correct sequence.
 *
 * CMD: gulp build
 */
gulp.task('build', ['build-sass','build-js', 'build-img']);

/**
 * Creates a zip file of the current project without any of the config and dev
 * files and saves it under the 'dist' folder.
 *
 * CMD: gulp zip
 */
gulp.task('zip', function(){
  return gulp.src( ZIP_SRC_ARR, ZIP_OPTS )
    .pipe( zip( BASE_NAME + '.zip' ) )
    .pipe( gulp.dest('dist') );
});
