'use strict';
var utils=require('../test-utils.js');


utils.basicExpectationsOnUnitTypes = function(error, response, body) {
    expect(response.statusCode).toBe(200);

    // Test Types 
    expect(body).toBeObject();
    expect(body.id).toBeNumber();
    expect(body.parent).toBeNumber();
}

utils.fullExpectationsOnUnitTypes = function(error, response, body) {
    utils.basicExpectationsOnChannelTypes(error, response, body);
    expect(body.title).toBeString();
    expect(body.overlays).toBeArray();
}


utils.basicExpectationsOnUnit = function(error, response, body,requested) {
    utils.basicExpectationsOnUnitTypes(error, response, body);
    utils.expectationsOnData(requested, body);
}

utils.fullExpectationsOnUnit = function(error, response, body, requested) {
    utils.fullExpectationsOnUnitTypes(error, response, body);
    utils.expectationsOnData(requested, body);
}

utils.unitFactory = {};

utils.unitFactory.getUnit = function() {
    return {};
}

utils.unitFactory.getUnitWithParent = function(parent) {
    return {
    "overlays": [{
        "id": 1,
        "type": "standard-annotation",
        "heading": "Capira Socrates Quiz Showcase",
        "body": "<h3>Welcome to the first preview of Capira Socrates!</h3>We made this preview to demonstrate the basic functionality.<br/><br/>We would appreciate feedback, to make sure that we are building what instructors need and students love.<br/><br/>Click the play button to go on!",
        "reaction": {
            "type": "showOverlay",
            "target": 0
        }
    }]
};
}

utils.unitFactory.getBrokenUnit = function() {
    return {};
}

utils.fetchUnitAs = function(unit, user, done, onResponse) {
    utils.request('GET', '/unit/' + unit.id, null,
        function(error, response, body) {
            utils.fullExpectationsOnUnit(error, response, body, unit);
            if (onResponse) onResponse(error, response, body);
            if (done) done();
            utils.log('Fetch Unit', user);
        }, user);
}

utils.createUnitAs = function(unit, user, done, onResponse) {
    utils.request('POST', '/unit', unit,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Create Unit', user);
        }, user);
}


utils.updateUnitAs = function(unit, user, done, onResponse) {
    utils.request('PUT', '/unit', unit,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Update Unit', user);
        }, user);
}

utils.updateUnitParentAs = function(unit, user, done, onResponse) {
    utils.request('PUT', '/unit/parent', unit,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Update Unit Parent', user);
        }, user);
}

utils.deleteChannelAs = function(unit, user, done, onResponse) {
    utils.request('DELETE', '/unit/' + unit.id, null,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Delete Unit', user);
        }, user);
}


module.exports=utils;
