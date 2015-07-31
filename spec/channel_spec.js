'use strict';
var cfg = require('./config.js');



function equals(a, b, fields) {
    return fields.every(function(field) {
        return a[field] === b[field];
    });
}

function expectationsOnChannelData(requested, response) {
    Object.keys(requested).every(function(key) {
        expect(requested[key]).toBe(requested[key]);
    });
}

function basicExpectationsOnChannelTypes(error, response, body) {
    expect(response.statusCode).toBe(200);

    // Test Types 
    expect(body).toBeObject();
    expect(body.id).toBeNumber();
    expect(body.parent).toBeNumber();
    expect(body.title).toBeString();
}

function fullExpectationsOnChannelTypes(error, response, body, requested) {
    basicExpectationsOnChannelTypes(error, response, body);
    expect(body.units).toBeArray();
    expect(body.folders).toBeArray();
}

function basicExpectationsOnChannel(error, response, body, requested) {
    basicExpectationsOnChannelTypes(error, response, body);
    expectationsOnChannelData(requested, body);
}

function fullExpectationsOnChannel(error, response, body, requested) {
    fullExpectationsOnChannelTypes(error, response, body);
    expectationsOnChannelData(requested, body);
}


function log(text, user) {
    console.log(text + (user ? ' as ' + user.name : ' anonymously') + '...');
}


function fetchChannelAs(channel, user, done, onResponse) {
    log('Fetch Channel', user);
    cfg.request('GET', '/channel/' + channel.id, null,
        function(error, response, body) {
            fullExpectationsOnChannel(error, response, body, channel);
            onResponse(error, response, body);
            if (done) {
                done();
            }
        }, user);
}

function createChannelAs(channel, user, done, onResponse) {
    log('Create Channel', user);
    cfg.request('POST', '/channel', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) {
                done();
            }
        }, user);
}

function deleteChannelAs(channel, user, done, onResponse) {
    log('Delete Channel', user);
    cfg.request('DELETE', '/channel', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) {
                done();
            }
        }, user);
}


function updateChannelAs(channel, user, done, onResponse) {
    log('Update Channel', user);
    cfg.request('PUT', '/channel', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) {
                done();
            }
        }, user);
}

function updateChannelParentAs(channel, user, done, onResponse) {
    log('Update Channel Parent', user);
    cfg.request('PUT', '/channel/parent', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) {
                done();
            }
        }, user);
}






describe('A Channel', function() {
    var user = cfg.usersFactory.getTestUser();
    var author = cfg.usersFactory.getTestAuthor();
    var admin = cfg.usersFactory.getTestAdmin();

    var testChannel = {
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
    var updatedChannel = {};

    /*
     * User Setup  
     * The primitive 'beforeAll' is part of Jasemine 2.1
     * which isn't released yet: https://github.com/jasmine/jasmine/issues/704
     * Therefore we use tests as a workaround.
     */
    it('coexists in this Tests with a authenticated User', function(done) {
        user.login(done, function(error, response) {
            expect(response.statusCode).toBe(200);
        });
    });

    it('coexists in this Tests with a authenticated Author', function(done) {
        author.login(done, function(error, response) {
            expect(response.statusCode).toBe(200);
        });
    });

    it('coexists in this Tests with a authenticated Admin', function(done) {
        admin.login(done, function(error, response) {
            expect(response.statusCode).toBe(200);
        });
    });


    it('can be cleaned up in this test', function(done) {
        fetchChannelAs(testChannel, admin, done, function(error, response, body) {
            body.folders.forEach(function(channel) {
                deleteChannelAs(channel, admin, null, function(error, response, body) {
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
        fetchChannelAs(testChannel, null, done, function(error, response, body) {
            expect(body.author).toBe(false);
            expect(body.admin).toBe(false);
        });
    });

    it('can be fetched as User', function(done) {
        fetchChannelAs(testChannel, user, done, function(error, response, body) {
            expect(body.author).toBe(false);
            expect(body.admin).toBe(false);
        });
    });

    it('can be fetched as Author', function(done) {
        fetchChannelAs(testChannel, author, done, function(error, response, body) {
            expect(body.author).toBe(true);
            expect(body.admin).toBe(false);
        });
    });

    it('can be fetched as Admin', function(done) {
        fetchChannelAs(testChannel, admin, done, function(error, response, body) {
            expect(body.author).toBe(true);
            expect(body.admin).toBe(true);
        });
    });





    /*
     *
     *   Create Channel 
     *
     */
    it('can not be created anonymously', function(done) {
        createChannelAs(testChannel.channels[0], null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can not be created by an User', function(done) {
        createChannelAs(testChannel.channels[0], user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be created by an Author', function(done) {
        createChannelAs(testChannel.channels[0], author, done, function(error, response, body) {
            basicExpectationsOnChannel(error, response, body, testChannel.channels[0]);
            testChannel.channels[0] = body;
        });
    });

    it('can be created by an Admin', function(done) {
        createChannelAs(testChannel.channels[1], admin, done, function(error, response, body) {
            basicExpectationsOnChannel(error, response, body, testChannel.channels[1]);
            testChannel.channels[1] = body;
        });
    });


    /*
     *
     *   Update Channel 
     *
     */
    it('can be updated by the client', function() {
        updatedChannel = JSON.parse(JSON.stringify(testChannel.channels[0]));
        updatedChannel.title += '_update';
        expect(true).toBe(true);
    });

    it('can not be updated anonymously', function(done) {
        updateChannelAs(updatedChannel, null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can not be updated by an User', function(done) {
        updateChannelAs(updatedChannel, user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be updated by an Author', function(done) {
        updatedChannel.title += '_author';
        updateChannelAs(updatedChannel, author, done, function(error, response, body) {
            basicExpectationsOnChannel(error, response, body, updatedChannel);
            expect(body.id).toBeNumber();

        });
    });

    it('can be updated by an Admin', function(done) {
        updatedChannel.title += '_admin';
        updateChannelAs(updatedChannel, admin, done, function(error, response, body) {
            basicExpectationsOnChannel(error, response, body, updatedChannel);
            expect(body.id).toBeNumber();

        });
    });



    /*
     *
     *   Update Parent of Channel 
     *
     */
    it('can be updated by the client', function() {
        updatedChannel = {
            id: testChannel.channels[0],
            parent: testChannel.channels[1]
        }
    });

    it('can not be updated anonymously', function(done) {
        updateChannelParentAs(updatedChannel, null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can not be updated by an User', function(done) {
        updateChannelParentAs(updatedChannel, user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be updated by an Author', function(done) {
        updateChannelParentAs(updatedChannel, author, done, function(error, response, body) {
            basicExpectationsOnChannel(error, response, body, updatedChannel);
            expect(body.id).toBeNumber();

        });
    });

    it('can be updated by an Admin', function(done) {
        updateChannelParentAs(updatedChannel, admin, done, function(error, response, body) {
            basicExpectationsOnChannel(error, response, body, updatedChannel);
            expect(body.id).toBeNumber();

        });
    });

    /*
     *
     *   Delete Channel 
     *
     */
    it('can not be deleted anonymously', function(done) {
        deleteChannelAs(testChannel.channels[0], null, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can not be deleted by an User', function(done) {
        deleteChannelAs(testChannel.channels[0], user, done, function(error, response, body) {
            expect(response.statusCode).toBe(401);
        });
    });

    it('can be deleted by an Author', function(done) {
        deleteChannelAs(testChannel.channels[0], author, done, function(error, response, body) {
            expect(response.statusCode).toBe(200);
        });
    });

    it('can be deleted by an Admin', function(done) {
        deleteChannelAs(testChannel.channels[1], admin, done, function(error, response, body) {
            expect(response.statusCode).toBe(200);
        });
    });




});
