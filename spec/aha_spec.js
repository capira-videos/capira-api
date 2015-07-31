'use strict';
var request = require('request');
var apiUrl = process.env.URL;


describe('A User', function() {
var time = Date.now();
var user = {
        name: 'test_'+time,
        password: '4378dsfa',
        email:    'test_'+time+'@capira.de'};



  it('can be created', function(done) {
    request({
      method: 'POST',
      uri:    apiUrl + '/signup',
      json:   true,
      body:   user
    }, function (error, response, body) {
    	expect(response.statusCode).toBe(201);
    	done();
	});
  });

  it('cant use existing mail for registration', function(done) {
    request({
      method: 'POST',
      uri:    apiUrl + '/signup',
      json:   true,
      body:   user
    }, function (error, response, body) {
    	expect(response.statusCode).toBe(406);
    	done();
	});
  });

  


});