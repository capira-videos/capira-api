'use strict';
var utils = require('./channel-utils.js');


describe('Channel', function() {
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

    var receivedChannel = {};

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


    it('can be cleaned up in this test', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            body.channels.forEach(function(channel) {
                utils.deleteChannelAs(channel, admin, null, function(error, response, body) {
                    expect(response.statusCode).toBe(200);
                });
            });
        });
    });



    /*
     *
     *   Fetch Channel 
     *
     */
    it('can be fetched anonymously', function(done) {
        utils.fetchChannelAs(testDataChannel, null, done, function(error, response, body) {

        });
    });

    it('can be fetched as User', function(done) {
        utils.fetchChannelAs(testDataChannel, user, done, function(error, response, body) {

        });
    });

    it('can be fetched as Author', function(done) {
        utils.fetchChannelAs(testDataChannel, author, done, function(error, response, body) {

        });
    });

    it('can be fetched as Admin', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            receivedChannel = body;
        });
    });



    /*
     *
     *   Create Channel 
     *
     */
    it('throws and error when created anonymously', function(done) {
        utils.createChannelAs(testDataChannel.channels[0], null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('has not been created', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });

    it('throws and error when created by an unauthorized User', function(done) {
        utils.createChannelAs(testDataChannel.channels[0], user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('has not been created', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });

    it('can be created by an Author', function(done) {
        utils.createChannelAs(testDataChannel.channels[0], author, done, function(error, response, body) {
            utils.basicExpectationsOnChannel(error, response, body, testDataChannel.channels[0]);
        });
    });

    it('then exists in its parent successfully', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(body.channels[0].title === testDataChannel.channels[0].title).toBe(true);
            testDataChannel.channels[0] = utils.copy(body.channels[0]);
        });
    });

    it('can be created by an Admin', function(done) {
        utils.createChannelAs(testDataChannel.channels[1], admin, done, function(error, response, body) {
            utils.basicExpectationsOnChannel(error, response, body, testDataChannel.channels[1]);
        });
    });

    it('then exists in its parent successfully', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(body.channels[1].title === testDataChannel.channels[1].title).toBe(true);
            testDataChannel.channels[1] = utils.copy(body.channels[1]);
            receivedChannel = body;
        });
    });


    /*
     *
     *   Update Channel 
     *
     */
    it('can be updated at the clientside', function(done) {
        testDataChannel.channels[0].title += '_update1';
        testDataChannel.channels[1].title += '_update2';
        done();
    });

    it('throws and error when updated anonymously', function(done) {
        utils.updateChannelAs(testDataChannel.channels[0], null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('has not been updated', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });

    it('throws and error when updated by an unauthorized User', function(done) {
        utils.updateChannelAs(testDataChannel.channels[1], user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('has not been updated', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });


    it('can be updated by an Author', function(done) {
        utils.updateChannelAs(testDataChannel.channels[0], author, done, function(error, response, body) {
            utils.basicExpectationsOnChannel(error, response, body, testDataChannel.channels[0]);
        });
    });

    it('has been updated successfully', function(done) {
        receivedChannel.channels[0].title = testDataChannel.channels[0].title;
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });

    it('can be updated by an Admin', function(done) {
        utils.updateChannelAs(testDataChannel.channels[1], admin, done, function(error, response, body) {
            utils.basicExpectationsOnChannel(error, response, body, testDataChannel.channels[1]);
        });
    });

    it('has been updated successfully', function(done) {
        receivedChannel.channels[1].title = testDataChannel.channels[1].title;
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });



    /*
     *
     *   Update Parent of Channel 
     *
     */
    var channelToUpdate = {};
    var newParent = {};
    it('parent can be updated at the clientside', function() {
        channelToUpdate = utils.copy(testDataChannel.channels[1]);
        newParent = receivedChannel.channels[0];
        channelToUpdate.parent = newParent.id;
    });

    it('throws and error when parent is updated anonymously', function(done) {
        utils.updateChannelParentAs(channelToUpdate, null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('parent has not been updated', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });

    it('throws and error when parent is updated by an unauthorized User', function(done) {
        utils.updateChannelParentAs(channelToUpdate, user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('parent has not been updated', function(done) {
        utils.fetchChannelAs(testDataChannel, admin, done, function(error, response, body) {
            expect(utils.objectEquals(body, receivedChannel)).toBe(true);
        });
    });

    it('parent can be updated by an Author', function(done) {
        utils.updateChannelParentAs(channelToUpdate, author, done, function(error, response, body) {
            utils.basicExpectationsOnChannel(error, response, body, channelToUpdate);
        });
    });

    it('parent has been updated successfully', function(done) {
        utils.fetchChannelAs(newParent, admin, done, function(error, response, body) {
            utils.expectationsOnData(channelToUpdate, body.channels[0]);
        });
    });

    it('parent can be updated at the clientside', function() {
        channelToUpdate = utils.copy(testDataChannel.channels[1]);
        newParent = receivedChannel;
        channelToUpdate.parent = newParent.id;
    });

    it('parent can be updated by an Admin', function(done) {
        utils.updateChannelParentAs(channelToUpdate, admin, done, function(error, response, body) {
            utils.basicExpectationsOnChannel(error, response, body, channelToUpdate);
        });
    });

    it('parent has been updated successfully', function(done) {
        utils.fetchChannelAs(newParent, admin, done, function(error, response, body) {
            utils.expectationsOnData(channelToUpdate, body.channels[1]);
        });
    });

    it('parent can be updated at the clientside', function() {
        channelToUpdate = utils.copy(testDataChannel.channels[1]);
        newParent = receivedChannel;
        channelToUpdate.parent = newParent.parent;
    });

    it('throws and error when parent is updated to an Channel in which Author is not permitted', function(done) {
        utils.updateChannelParentAs(channelToUpdate, author, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    /*
     *
     *   Delete Channel 
     *
     */
    it('can not be deleted anonymously', function(done) {
        utils.deleteChannelAs(testDataChannel.channels[0], null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can not be deleted by an User', function(done) {
        utils.deleteChannelAs(testDataChannel.channels[0], user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be deleted by an Author', function(done) {
        utils.deleteChannelAs(testDataChannel.channels[0], author, done, function(error, response, body) {
            expect(response.statusCode).toBe(200);
        });
    });

    it('can be deleted by an Admin', function(done) {
        utils.deleteChannelAs(testDataChannel.channels[1], admin, done, function(error, response, body) {
            expect(response.statusCode).toBe(200);
        });
    });




});
