'use strict';

module.exports=require('./config.js');


module.exports.copy = function(x)  {
    return JSON.parse(JSON.stringify(x));
}

module.exports.equals=function (a, b, fields) {
    return fields.every(function(field) {
        return a[field] === b[field];
    });
}

module.exports.objectEquals=function (a, b) {
    return JSON.stringify(a) === JSON.stringify(b);
}

module.exports.booleanize=function (x) {
    return x ? x : false;
}

module.exports.expectationsOnData=function (requested, response) {
    Object.keys(requested).every(function(key) {
        expect(requested[key]).toBe(response[key]);
    });
}

module.exports.log=function (text, user) {
    //console.log(text + (user ? ' as ' + user.name : ' anonymously') + '...');
}


