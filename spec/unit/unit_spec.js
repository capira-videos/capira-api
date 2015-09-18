'use strict';
var utils = require('./unit-utils.js');


describe('Unit', function() {
    var user = utils.usersFactory.getTestUser();
    var author = utils.usersFactory.getTestAuthor();
    var admin = utils.usersFactory.getTestAdmin();

    var testDataChannel = {
        id: 352,
        title: 'automated_testing_channel',
        channels: [{
            title: 'automated_testing_subchannel1',
            parent: 352
        }, {
            title: 'automated_testing_subchannel2',
            parent: 352
        }]
    };

    var unit = utils.unitFactory.getUnitWithParent(testDataChannel.channels[0]);

    var receivedUnit1 = {};
    var receivedUnit2 = {};

    /*
     * User Setup  
     * The primitive 'beforeAll' is part of Jasemine 2.1
     * which isn't released yet: https://github.com/jasmine/jasmine/issues/704
     * Therefore we use tests as a workaround.
     */
    it('coexists in this Tests with an authenticated User', function(done) {
        user.login(done, function(error, response) {
            expect(response.statusCode).toBe(200);
        });
    });

    it('coexists in this Tests with an authenticated Author', function(done) {
        author.login(done, function(error, response) {
            expect(response.statusCode).toBe(200);
        });
    });

    it('coexists in this Tests with an authenticated Admin', function(done) {
        admin.login(done, function(error, response) {
            expect(response.statusCode).toBe(200);
        });
    });

    /*
     *
     *   Create Unit 
     *
     */
    it('throws and error when created anonymously', function(done) {
        utils.createUnitAs(unit, null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('throws and error when created by an unauthorized User', function(done) {
        utils.createUnitAs(unit, user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be created by an Author', function(done) {
        utils.createUnitAs(unit, author, done, function(error, response, body) {
            receivedUnit1 = body; // now we have a variable for that
            utils.basicExpectationsOnUnit(error, response, body, unit);
        });
    });

    it('then can be fetched successfully', function(done) {
        utils.fetchUnitAs(receivedUnit1, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedUnit1)).toBe(true);
        });
    });

    it('can be created by an Admin', function(done) {
        utils.createChannelAs(testDataChannel.channels[1], admin, done, function(error, response, body) {
            receivedUnit2 = body; // now we have a variable for that
            utils.basicExpectationsOnUnit(error, response, body, unit);
        });
    });

    it('then can be fetched successfully', function(done) {
        utils.fetchUnitAs(receivedUnit2, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedUnit2)).toBe(true);
        });
    });


    /*
     *
     *   Fetch Channel 
     *
     */
    it('can be fetched anonymously', function(done) {
        utils.fetchUnitAs(receivedUnit1, null, done);
    });

    it('can be fetched as User', function(done) {
        utils.fetchUnitAs(receivedUnit1, user, done);
    });

    it('can be fetched as Author', function(done) {
        utils.fetchUnitAs(receivedUnit1, author, done);
    });

    it('can be fetched as Admin', function(done) {
        utils.fetchUnitAs(receivedUnit1, admin, done);
    });


    /*
     *
     *   Update Channel 
     *
     */
    it('can be updated at the clientside', function(done) {
        receivedUnit1.title += '_update1';
        receivedUnit2.title += '_update2';
        done();
    });

    it('throws and error when updated anonymously', function(done) {
        utils.updateUnitAs(receivedUnit1, null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('throws and error when updated by an unauthorized User', function(done) {
        utils.updateUnitAs(receivedUnit1, user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be updated by an Author', function(done) {
        utils.updateUnitAs(receivedUnit1, author, done, function(error, response, body) {
            utils.fullExpectationsOnUnit(error, response, body, receivedUnit1);
        });
    });

    it('has been updated successfully', function(done) {
        utils.fetchUnitAs(receivedUnit1, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedUnit1)).toBe(true);
        });
    });

    it('can be updated by an Admin', function(done) {
        utils.updateUnitAs(receivedUnit2, admin, done, function(error, response, body) {
            utils.fullExpectationsOnUnit(error, response, body, receivedUnit2);
        });
    });

    it('has been updated successfully', function(done) {
        utils.fetchUnitAs(receivedUnit2, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedUnit2)).toBe(true);
        });
    });



    /*
     *
     *   Update Parent of Channel 
     *
     */
    // todo

    /*
     *
     *   Delete Channel 
     *
     */
    it('can not be deleted anonymously', function(done) {
        utils.deleteUnitAs(receivedUnit1, null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can not be deleted by an User', function(done) {
        utils.deleteUnitAs(receivedUnit1, user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be deleted by an Author', function(done) {
        utils.deleteUnitAs(receivedUnit1, author, done, function(error, response, body) {
            expect(response.statusCode).toBe(200);
        });
    });

    it('can be deleted by an Admin', function(done) {
        utils.deleteUnitAs(receivedUnit2, admin, done, function(error, response, body) {
            expect(response.statusCode).toBe(200);
        });
    });



});
