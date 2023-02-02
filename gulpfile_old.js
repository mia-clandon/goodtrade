"use strict";

/**
 * Сборка для backend.
 * Возможно уже устарела.
 * @type {Gulp}
 */

let gulp    = require('gulp')
    ,uglify = require('gulp-uglify')
    ,concat = require('gulp-concat')
    ,rename = require('gulp-rename')
    ,babel  = require('gulp-babel')
    ,clean  = require('gulp-clean')
    ,run    = require('gulp-run-command').default
;

// сборка скриптов для страницы импорта товаров.
gulp.task('import_scripts', function() {
    let import_script_path = 'backend/web/js/import/';
    //noinspection JSUnresolvedFunction
    return gulp.src(import_script_path + '*.js')
        .pipe(concat('import.js'))
        .pipe(rename('import.min.js'))
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(uglify({
            mangle: true,
            output: {
                beautify: true,
            }
        }))
        .pipe(gulp.dest(import_script_path + 'dist'));
    //TODO: наверное нужно добавить сюда clean_backend_web_assets (убрав с default)
});

// сборка скриптов BackEnd.
gulp.task('compile_backend', function() {
    let path = 'backend/web/js/';
    [
        'components/form/controls/', // сборка контролов.
        'category/', // сборка страниц категорий.
        'forms/product/', // сборка страницы редактирования товара.
        'translate/' // переводы.
    ]
        .forEach(function(dir) {
            gulp.src(path + dir + '*.js')
                .pipe(babel({
                    presets: ['es2015']
                }))
                .pipe(uglify({
                    mangle: true,
                    output: {
                        beautify: false,
                    }
                }))
                .pipe(gulp.dest(path + dir + 'dist'));
    });
});

// подчистка backend/web/assets.
gulp.task('clean_backend_web_assets', function () {
    return gulp.src(['backend/web/assets/*', '!.gitignore'], {read: false})
        .pipe(clean({force: true}));
});

// компилирование scss (compass).
gulp.task('clean_sprites', run('rm -rf frontend/web/img/sprites/*'));
gulp.task('clean_css', run('rm -rf frontend/web/css/*'));
gulp.task('compass', ['clean_sprites', 'clean_css'], run('compass compile', {
    env: { NODE_ENV: 'production' }
}));

// компилирование scss (compass) backend.
gulp.task('clean_css_backend', run('rm -rf backend/web/css/*'));
gulp.task('compass_backend', ['clean_css_backend'], run('compass compile backend/web', {
    env: { NODE_ENV: 'production' }
}));

gulp.task('default', ['import_scripts', 'clean_backend_web_assets', 'compass', 'compass_backend', 'compile_backend']);