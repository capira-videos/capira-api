'use strict';
var frisby = require('frisby');
var apiUrl = process.env.URL;
 
frisby.create('POST login')
    .post(apiUrl + '/login', {
        name: 'test',
        password: 'test'
    }, {
        json: true
    })
    .expectStatus(200)
    .after(function(body, res) {
        var setCookie = res.headers['set-cookie'];
        var cookie = '';

        console.log('SetCookie:---------- ' + setCookie);
        var setCookie2 = [setCookie[0], setCookie[2]];
        console.log('SetCookie2:---------- ' + setCookie2);

        if (Array.isArray(setCookie2)) {
            for (var i = 0, len = setCookie2.length; i < len; i++) {
                cookie += setCookie2[i].split(';')[0];
                if (i < len - 1) {
                    cookie += '; ';
                }
            }
        }

        // Fetch account
        frisby.create('Fetch profile')
            .get(apiUrl + '/me', {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Cookie': cookie,
                }
            })
            .inspectJSON()
            .toss();
    }).toss();
