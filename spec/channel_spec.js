'use strict';
require('jasmine-expect');
it = require('jasmine-async-errors').wrap(it);
var request = require('request');
var config = require('./config.js');

describe('A Channel', function() {
    it('can be logged in', function(done) {
        config.user.login(done);
    });

    it('can be fetched', function(done) {
        request({
            method: 'GET',
            uri: config.url + '/channel/1',
            json: true,
        }, function(error, response, body) {
            expect(response.statusCode).toBe(200);
            expect(body).toBeObject();
            expect(body.title).toBeString();
            done();
        });
    });

});
