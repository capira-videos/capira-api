'use strict';
var request = require('request');

var createUser = function(user, url) {
    user.getFields = function(fields) {
        var that = this;
        return fields.reduce(function(a, e) {
            a[e] = that[e];
            return a;
        }, {});
    };
    user.login = function(done) {
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
            var setCookies = response.headers['set-cookie'];

            /* Ugly hack, because somehow the server returns 2 XSRF-Tokens.*/
            that.cookie = setCookies[0].split(';')[0] +'; '+
                          setCookies[2].split(';')[0];
            console.log('  User "' + that.name + '" is logged in now.');
            console.log('  Cookies are "' + that.cookie);
            done();
        });
    };
    return user;
};



module.exports = function(url) {
    return {
        getTestUser: function() {
            return createUser({
                name: 'test',
                password: 'test'
            }, url);
        },

        getRandomUser: function() {
            var time = Date.now();
            return createUser({
                name: 'test_' + time,
                password: '12345678',
                email: 'test_' + time + '@capira.de'
            }, url);
        }
    };
};
