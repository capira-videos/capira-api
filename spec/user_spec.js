'use strict';
var request = require('request');
var config = require('./config.js');

describe('A User', function() {

    var user = config.usersFactory.getRandomUser();

    it('can be created', function(done) {
        request({
            method: 'POST',
            uri: config.url + '/signup',
            json: true,
            body: user
        }, function(error, response, body) {
            expect(response.statusCode).toBe(201);
            done();
        });
    });

    it('can not use existing mail for registration', function(done) {
        request({
            method: 'POST',
            uri: config.url + '/signup',
            json: true,
            body: user
        }, function(error, response, body) {
            expect(response.statusCode).toBe(406);
            done();
        });
    });




});
