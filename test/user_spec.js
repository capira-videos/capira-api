'use strict';
var frisby = require('frisby');
var apiUrl = process.env.URL;


var world = {users:[],units:[],channels:[]};
world.users.push( {
        name: 'tessdd817f987sf234k',
        password: '394asdf8yrewfh',
        email: 'teufas1dsdfa234@capira.de'
    });

frisby.create('Create a Capira account')
    .post(apiUrl + '/signup', {
            'email':    world.users[0].email,
            'name':     world.users[0].name,
            'password': world.users[0].password 
        }, {json: true})
    .expectStatus(201)
    .expectJSON({
        'name':  world.users[0].name,
        'email': world.users[0].email
    })
    .expectJSONTypes({
        id: Number
    })
    .afterJSON( function(json) {
        console.log(json);
        frisby.create('Create existing User')
            .post(apiUrl + '/signup', {
                    'email':    world.users[0].email,
                    'name':     world.users[0].name,
                    'password': world.users[0].password 
                }, {json: true})
                .expectStatus(406)
                .expectJSONTypes({
                    'error':  String
                    })
                .afterJSON( function(json) {
                    console.log(json);
                })
            .toss();
        })
    .toss();

frisby.create('Login User')
    .post(apiUrl + '/login', {
            name:     'test',
            password: 'test'
        }, {json: true})
    .expectStatus(200)
    .after(function(err, res, body) {
        //console.log(body);
        //console.log(res);
        //console.log(err);
        var setCookie = res.headers['set-cookie'];
        var cookie =    '';

        var setCookie2 = [setCookie[0], setCookie[2]];

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
                    'Accept':       'application/json',
                    'Cookie':       cookie
                }
            })
            .toss();
    }).toss();