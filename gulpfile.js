'use strict';
var gulp = require('gulp');
var apidoc = require('gulp-apidoc');
 
gulp.task('apidoc', function(cb){
          apidoc.exec({
            src: "server/api",
            dest: "server/docs/"
          }, cb);
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