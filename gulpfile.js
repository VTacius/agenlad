'use strict';

var pathos = {
    css: {
        sass: './app/web/sass/*scss',
        dev: './web/css/src/',
        prod: './web/css/'
    },
    js: {
        dev: './app/web/js/',
        src: './web/js/src/',
        prod: './web/js/'
    },
    vendor: {
        js: {
            path: './app/web/js/vendor',
            lib: [
                './bower_components/jquery/dist/jquery.js',
                './bower_components/jquery-ui/ui/*.js',
                './bower_components/boostrap-sass/assets/javascripts/bootstrap/*.js',
                './bower_components/handlebars/handlebars.js',
                './bower_components/jquery.validate/dist/jquery.validate.js',
                './bower_components/jquery.validate/src/localization/messages_es.js',
                './bower_components/handlebars/handlebars.js',
                './bower_components/jQuery-Mask-Plugin/dist/jquery.mask.js',
            ]
        }
    }
};

var gulp = require('gulp');
var sass = require('gulp-sass');
var copy = require('copy-dir');
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
    .pipe(gulp.dest(pathos.css.dev));
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
    gulp.src(pathos.vendor.js.lib)
    .pipe(gulp.dest(pathos.vendor.js.path));
});

/* Tarea previa independiente: Muevo images de jquery-ui al directorio de producción */
gulp.task('image', function(){
    gulp.src('./bower_components/jquery-ui/themes/smoothness/images/')
    .pipe(gulp.dest(pathos.css.prod));
    gulp.src('./bower_components/jquery-ui/themes/smoothness/images/*')
    .pipe(gulp.dest(pathos.css.prod + 'images/'));
});

/* Tarea previa independiente: Muevo fuentes de bootstrap-sass al directorio de producción */
gulp.task('fonts', function(){
    gulp.src('./bower_components/boostrap-sass/assets/fonts/bootstrap/*')
    .pipe(gulp.dest(pathos.css.prod + 'fonts/'));
});

/* Tarea previa independiente: Muevo data de desarrollo a producción */
gulp.task('data', function(){
    gulp.src(pathos.js.dev + '/data/*')
    .pipe(gulp.dest(pathos.js.prod + '/data'))
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

gulp.task('default', ['minicss', 'minijs', 'image', 'fonts', 'data']);
