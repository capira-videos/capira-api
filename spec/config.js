'use strict';
require('jasmine-expect');
it = require('jasmine-async-errors').wrap(it);
var request = require('request');
var user = require('./user_helper.js');

var apiUrl = process.env.URL;
apiUrl = apiUrl ? apiUrl : 'http://capira.de/build/api_v2';

module.exports = {
    usersFactory: user(apiUrl),
    request: function(method, url, body, onResponse, user) {
        request({
            method: method,
            uri: apiUrl + url,
            json: true,
            body: body,
            headers: {
                Cookie: user ? user.cookie : ''
            }
        }, onResponse);
    }
};

console.log(
    '  **************************************\n' +
    '  **                                  **\n' +
    '  **     Capira API Testing Suite     **\n' +
    '  **                                  **\n' +
    '  **************************************\n' +
    '  Configuration: \n' +
    '  Server URL: ' + apiUrl + '\n' +
    '\n');
