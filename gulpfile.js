'use strict';
var gulp = require('gulp');

/*
var apidoc = require('gulp-apidoc');

gulp.task('apidoc', function(cb) {
    apidoc.exec({
        src:  "api/routes/",
        dest: "docs/"
    }, cb);
});

*/

var apidoc = require('gulp-api-doc');
 
gulp.task('doc', function() {
    return gulp.src('api/routes/')
        .pipe(apidoc())
        .pipe(gulp.dest('docs'));
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
