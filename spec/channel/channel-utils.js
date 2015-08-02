'use strict';
var utils=require('../test-utils.js');


utils.basicExpectationsOnChannelTypes = function(error, response, body) {
    expect(response.statusCode).toBe(200);

    // Test Types 
    expect(body).toBeObject();
    expect(body.id).toBeNumber();
    expect(body.parent).toBeNumber();
}

utils.fullExpectationsOnChannelTypes = function(error, response, body) {
    utils.basicExpectationsOnChannelTypes(error, response, body);
    expect(body.title).toBeString();
    expect(body.units).toBeArray();
    expect(body.channels).toBeArray();
}



utils.expectationsOnChannelPermissions = function(body, user) {
    user = user ? user : {};
    expect(body.author === user.author).toBe(true);
    expect(body.admin === user.admin).toBe(true);
}

utils.basicExpectationsOnChannel = function(error, response, body,requested) {
    utils.basicExpectationsOnChannelTypes(error, response, body);
    utils.expectationsOnData(requested, body);
}

utils.fullExpectationsOnChannel = function(error, response, body, requested, user) {
    utils.fullExpectationsOnChannelTypes(error, response, body);
    utils.expectationsOnData(requested, body);
    if (user) {
        utils.expectationsOnChannelPermissions(body, user);
    }
}



utils.fetchChannelAs = function(channel, user, done, onResponse) {
    utils.request('GET', '/channel/editor/' + channel.id, null,
        function(error, response, body) {
            utils.fullExpectationsOnChannel(error, response, body, channel, user);
            if (onResponse) onResponse(error, response, body);
            if (done) done();
            utils.log('Fetch Channel', user);
        }, user);
}

utils.createChannelAs = function(channel, user, done, onResponse) {
    utils.request('POST', '/channel', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Create Channel', user);
        }, user);
}


utils.updateChannelAs = function(channel, user, done, onResponse) {
    utils.request('PUT', '/channel', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Update Channel', user);
        }, user);
}

utils.updateChannelParentAs = function(channel, user, done, onResponse) {
    utils.request('PUT', '/channel/parent', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Update Channel Parent', user);
        }, user);
}

utils.deleteChannelAs = function(channel, user, done, onResponse) {
    utils.request('DELETE', '/channel', channel,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Delete Channel', user);
        }, user);
}


module.exports=utils;
