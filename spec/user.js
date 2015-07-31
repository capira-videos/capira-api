'use strict';
var request = require('request');

module.exports = function(url) {
    return {
        name: 'test',
        password: 'test',

        getFields: function(fields) {
            var that = this;
            return fields.reduce(function(a, e) {
                a[e] = that[e];
                return a;
            }, {});
        },
        login: function(done) {
            var that = this;
            request({
                method: 'POST',
                uri: url + '/login',
                json: true,
                body: {
                    name: that.name,
                    password: that.password
                }
            }, function(error, response, body) {
                that.cookies = response.headers['set-cookie'];
                console.log('  User "' + that.name + '" is logged in now.');
                done();
            });
        }
    };
}
