'use strict';

var gulp          = require('gulp');
var sass          = require('gulp-sass');
var postcss       = require('gulp-postcss');
var cssnano       = require('cssnano');
var rename        = require('gulp-rename');
var autoprefixer  = require('autoprefixer');
var rimraf        = require('rimraf');
var runSequence   = require('run-sequence');
var zip           = require('gulp-zip');
var uglify        = require('gulp-uglify');
var babel         = require('gulp-babel');
var webpackStream = require('webpack-stream');
var webpack       = require('webpack');

var dir = {
  src: {
    css: 'src/css',
    js:  'src/js'
  },
  dist: {
    css: 'assets/css',
    js:  'assets/js'
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
 * Build javascript
 */
gulp.task('js', function() {
  runSequence('js:app');
});
gulp.task('js:app', function() {
  return jsCompile('app.js');
});

function jsCompile(distFileName) {
  return gulp.src(dir.src.js + '/' + distFileName)
    .pipe(webpackStream({
      output: {
        filename: distFileName
      },
      externals: {
        jquery: 'jQuery',
        _: '_',
        backbone: 'Backbone'
      },
      mode: 'none',
      module: {
        rules: [{
          use: {
            loader: 'babel-loader',
            options: {
              presets: [
                [
                  "env", {
                    "modules": false,
                    "targets": {
                      "browsers": ['last 2 versions']
                    }
                  }
                ]
              ]
            }
          }
        }]
      }
    }, webpack))
    .pipe(gulp.dest(dir.dist.js))
    .on('end', function() {
      gulp.src([dir.dist.js + '/' + distFileName])
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(dir.dist.js));
    });
}

/**
 * Build
 */
gulp.task('build', ['css', 'js']);

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
        '!.phpcs.xml.dist',
        '!phpmd.ruleset.xml'
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
  gulp.watch([dir.src.js + '/**/*.js'] , ['js']);
});
