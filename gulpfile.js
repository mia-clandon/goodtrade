var gulp = require('gulp');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var cleanCSS = require('gulp-clean-css');

var sass = require('gulp-sass')(require('sass'));

var paths = {
    styles: {
        src: 'frontend/web/scss_new/**/*.scss',
        dest: 'frontend/web/css_new/'
    }
};

function styles() {
    return gulp.src(paths.styles.src)
        .pipe(sass())
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(paths.styles.dest));
}

exports.styles = styles;