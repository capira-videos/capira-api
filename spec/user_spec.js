'use strict';
var cfg = require('./config.js');
var user = cfg.usersFactory.getRandomUser();

describe('A User', function() {

    it('can be created', function(done) {
        cfg.request('POST', '/signup', user,
            function(error, response) {
                expect(response.statusCode).toBe(201);
                done();
            });
    });

    it('can not use existing mail for registration', function(done) {
        cfg.request('POST', '/signup', user,
            function(error, response) {
                expect(response.statusCode).toBe(406);
                done();
            });
    });

});
