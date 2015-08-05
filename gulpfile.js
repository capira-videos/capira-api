'use strict';
var gulp = require('gulp');
var apidoc = require('gulp-api-doc');
var clean = require('gulp-clean');
var todo = require('gulp-todo');


gulp.task('doc', function() {
    return gulp.src('api/routes/')
        .pipe(apidoc())
        .pipe(gulp.dest('docs'));
});

gulp.task('clean', function() {
    return gulp.src(['dist/', '!**/*/sftp-config.json'], {
            read: false
        })
        .pipe(clean());
});

gulp.task('default', ['copy'], function() {
    return gulp.src(['config/sftp-config.json'], {
        base: 'config'
    }).pipe(gulp.dest('dist/'));
});

gulp.task('copy', ['clean'], function() {
    return gulp.src(['api/**/*.php', '!api/vendor/slim/slim/tests/**/*', 'api/**/.htaccess', '!api/tools/**/*'], {
        base: 'api'
    }).pipe(gulp.dest('dist'));
});




// generate a todo.md from your javascript files 
gulp.task('todo', function() {
    gulp.src(['spec/**/*.js', 'api/**/*.php'])
        .pipe(todo())
        .pipe(gulp.dest('./')) //output todo.md as markdown 
        .pipe(todo.reporter('json', {
            fileName: 'todo.json'
        }))
        .pipe(gulp.dest('./')); //output todo.json as json         // -> Will output a TODO.md with your todos 
});






/*
var swaggerGenerator = require('gulp-apidoc-swagger');

gulp.task('swag', function() {
    swaggerGenerator.exec({
        src: "api/routes/",
        dest: "spec/"
    });
});
*/
