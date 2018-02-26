'use strict';

var gulp         = require('gulp');
var sass         = require('gulp-sass');
var postcss      = require('gulp-postcss');
var cssnano      = require('cssnano');
var rename       = require('gulp-rename');
var autoprefixer = require('autoprefixer');
var rimraf       = require('rimraf');
var runSequence  = require('run-sequence');
var zip          = require('gulp-zip');

var dir = {
  src: {
    css: 'src/css'
  },
  dist: {
    css: 'assets/css'
  }
}

/**
 * Build CSS
 */
gulp.task('css', ['remove-css'], function() {
  return sassCompile(dir.src.css + '/*.scss', dir.dist.css)
    .on('end', function() {
      return gulp.src(dir.src.css + '/**/*.php')
        .pipe(gulp.dest(dir.dist.css));
    });
});

/**
 * Remove directory for copied node modules
 */
gulp.task('remove-css', function(cb) {
  rimraf(dir.dist.css, cb);
});

function sassCompile(src, dest) {
  return gulp.src(src)
    .pipe(sass())
    .pipe(postcss([
      autoprefixer({
        browsers: ['last 2 versions'],
        cascade: false
      })
    ]))
    .pipe(gulp.dest(dest))
    .pipe(postcss([
      cssnano({
        'zindex': false
      })
    ]))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(dest))
}

/**
 * Build
 */
gulp.task('build', ['css']);

/**
 * Creates the zip file
 * This command must be build beforehand on Travis CI !!
 */
gulp.task('zip', function(){
  return gulp.src(
      [
        '**',
        '!tests',
        '!test/**',
        '!node_modules',
        '!node_modules/**',
        '!package.json',
        '!gulpfile.js',
        '!yarn.lock',
        '!composer.json',
        '!composer.lock',
        '!phpcs.ruleset.xml',
        '!phpunit.xml.dist'
      ],
      {base: './'}
    )
    .pipe(zip('snow-monkey-bbpress-support.zip'))
    .pipe(gulp.dest('./'));
});

/**
 * Auto build and browsersync
 */
gulp.task('default', ['build'], function() {
  gulp.watch([dir.src.css + '/**/*.scss'], ['css']);
});
