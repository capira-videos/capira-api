'use strict';
var cfg = require('./config.js');
var user = cfg.usersFactory.createRandomUser();

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


      it('can be created', function(done) {
        cfg.request('POST', '/signup', cfg.usersFactory.getTestUser(),
            function(error, response) {
                expect(response.statusCode).toBe(201);
                done();
            });
    });

        it('can be created', function(done) {
        cfg.request('POST', '/signup', cfg.usersFactory.getTestAuthor(),
            function(error, response) {
                expect(response.statusCode).toBe(201);
                done();
            });
    });

          it('can be created', function(done) {
        cfg.request('POST', '/signup', cfg.usersFactory.getTestAdmin(),
            function(error, response) {
                expect(response.statusCode).toBe(201);
                done();
            });
    });

});
