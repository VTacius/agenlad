'use strict';

var pathos = {
    css: {
        sass: './app/web/sass/*scss',
        dev: './web/css/src',
        prod: './web/css/'
    },
    js: {
        dev: './app/web/js/*.js',
        src: './web/js/src',
        prod: './web/js'
    },
    vendor: {
        js: {
            path: './app/js/vendor',
            lib: [
                './bower_components/jquery/dist/jquery.js',
                './bower_components/boostrap-sass/assets/javascripts/bootstrap/*.js',
                './bower_components/handlebars/handlebars.js'
            ]
        }
    }
};

var gulp = require('gulp');
var sass = require('gulp-sass');
// Ahora, uglificamos
var mapa = require('gulp-sourcemaps');
var minijs = require('gulp-uglify');
var minicss = require('gulp-minify-css');

/* Empieza el trabajo con javascript */
var include = require('gulp-include');

/* Luego, las adicionales al fin */
var util = require('gulp-util');

/* Trabajo con SASS/CSS */
gulp.task('estilo', function(){
    gulp.src(pathos.css.sass)
    .pipe(sass())
    .on('error', util.log)
    .pipe(gulp.dest('./web/css/src'));
});

/* Minificamos el css */
gulp.task('minicss', function(){
    gulp.src(pathos.css.sass)
    .pipe(sass())
    .on('error', util.log)
    .pipe(gulp.dest(pathos.css.dev))
    .pipe(mapa.init())
    .pipe(minicss())
    .on('error', util.log)
    .pipe(mapa.write())
    .pipe(gulp.dest(pathos.css.prod));
});

/* Tarea previa: Muevo los script de vendor en bower_components a app/js/vendor */
gulp.task('copiar', function(){
    gulp.src(pathos.vendor.js.lib, {read: false})
    .pipe(gulp.dest(pathos.vendor.js.path));
});

/* Trabajo con JavaScript */
gulp.task('guion', ['copiar'], function(){
    gulp.src(pathos.js.dev)
    .pipe(include())
    .pipe(gulp.dest(pathos.js.src));
});

/* Minificamos después para obtener un mapa más limpio*/
gulp.task('minijs', ['copiar'], function(){
    gulp.src(pathos.js.dev)
    .pipe(include())
    .pipe(gulp.dest(pathos.js.src))
    .pipe(mapa.init())
    .pipe(minijs())
    .on('error', util.log)
    .pipe(mapa.write())
    .pipe(gulp.dest(pathos.js.prod));
}); 

/* La tarea por defecto ejecuta las tareas más principales */

gulp.task('default', ['minicss', 'minijs']);
