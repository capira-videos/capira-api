'use strict';
var gulp = require('gulp');
var apidoc = require('gulp-api-doc');
var clean = require('gulp-clean');

gulp.task('doc', function() {
    return gulp.src('api/routes/')
        .pipe(apidoc())
        .pipe(gulp.dest('docs'));
});
 
gulp.task('clean', function () {
    return gulp.src('dist/', {read: false})
        .pipe(clean());
});

gulp.task('default',['clean'],  function () {
        return gulp.src(['api/**/*.php', 'api/**/.htaccess', '!api/tools/**/*', '!api/vendor/slim/slim/tests'], {
            base: 'api'
        }).pipe(gulp.dest('dist'));
    });

/*
var swaggerGenerator = require('gulp-apidoc-swagger');
 
gulp.task('swaggerGenerator', function(){
          swaggerGenerator.exec({
            src: "server/SwaggerServer/",
            dest: "spec/"
          });
});

*/
