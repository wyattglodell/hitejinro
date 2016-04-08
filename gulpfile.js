var gulp = require('gulp'),
	changed = require('gulp-changed'),
	sass = require('gulp-sass'),
	prefix = require('gulp-autoprefixer'),
	minify = require('gulp-minify-css');

var src = './custom/inc/scss/',
	dist = './public/css/',
	srcStyles = src + '**/*.scss',
	distStyles = dist + '**/*.css';

function handleError(err) {
	console.log(err.toString());
	this.emit('end');
}

gulp.task('compile', function() {
	gulp.src(srcStyles)
    	.pipe(sass()).on('error', handleError)
    	.pipe(prefix())
    	.pipe(gulp.dest(dist));
});

gulp.task('watch', function() {
	gulp.watch(srcStyles, ['compile']);
});

gulp.task('default', ['compile', 'watch']);