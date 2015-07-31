'use strict';
require('jasmine-expect');
it = require('jasmine-async-errors').wrap(it);
var cfg = require('./config.js');
var user = cfg.usersFactory.getTestUser();

describe('A Channel', function() {
    
    it('can be logged in', function(done) {
        user.login(done);
    });

    it('can be fetched', function(done) {
        cfg.request('GET', '/channel/164', user,
            function(error, response, body) {
                expect(response.statusCode).toBe(200);
                expect(body).toBeObject();
                expect(body.title).toBeString();
                expect(body.units).toBeArray();
                expect(body.folders).toBeArray();

                done();
            });
    });

});
