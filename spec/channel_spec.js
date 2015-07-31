'use strict';
var cfg = require('./config.js');

describe('A Channel', function() {
    var user = cfg.usersFactory.getTestUser();
    var channelId = 164;

    it('can be fetched anonymously', function(done) {
        cfg.request('GET', '/channel/' + channelId, null,
            function(error, response, body) {
                expect(response.statusCode).toBe(200);
                expect(body).toBeObject();
                expect(body.title).toBeString();
                expect(body.units).toBeArray();
                expect(body.folders).toBeArray();
                done();
            });
    });

    it('has a User state', function(done) {
        user.login(done);
    });

    it('can be fetched as author', function(done) {
        cfg.request('GET', '/channel/' + channelId, null,
            function(error, response, body) {
                expect(response.statusCode).toBe(200);
                expect(body).toBeObject();
                expect(body.title).toBeString();
                expect(body.units).toBeArray();
                expect(body.folders).toBeArray();
                expect(body.author).toBe(true);
                done();
            }, user);
    });

    it('can be fetched as admin', function(done) {
        cfg.request('GET', '/channel/' + channelId, null,
            function(error, response, body) {
                expect(response.statusCode).toBe(200);
                expect(body).toBeObject();
                expect(body.title).toBeString();
                expect(body.units).toBeArray();
                expect(body.folders).toBeArray();
                expect(body.author).toBe(true);
                expect(body.admin).toBe(true);
                done();
            }, user);
    });

});
