'use strict';
var user = require('./user.js');
var apiUrl = process.env.URL;
apiUrl = apiUrl ? apiUrl : 'http://localhost:8888/api';
module.exports = {
    url: apiUrl,
    user: user(apiUrl)
};

console.log(
	'  **************************************\n'+
	'  **                                  **\n'+
	'  **     Capira API Testing Suite     **\n'+
	'  **                                  **\n'+
	'  **************************************\n'+
	'  Configuration: \n'+
	'  Server URL: '+module.exports.url+'\n'+
	'  User: '+module.exports.user.name+'\n'+
	'\n');