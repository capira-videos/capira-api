var frisby = require('frisby');
var URL = process.env.URL

frisby.create('POST login')
	.post(URL + '/user/login', {
		name:			'test',
		password: 'test'
		}, {json: true})
	.expectStatus(200)
	.after( function(body,res) {
		var setCookie = res.headers['set-cookie']
		var cookie = ''

		console.log('SetCookie:---------- ' + setCookie)
		setCookie2 = [setCookie[0], setCookie[2]]
		console.log('SetCookie2:---------- ' + setCookie2)
		
    if (Array.isArray(setCookie2)) {
				for (var i = 0, len = setCookie2.length; i < len; i++) {
						cookie += setCookie2[i].split(';')[0]
						if (i < len - 1)
							 cookie += '; '
				}
		}

		// Fetch account
		frisby.create('Fetch profile')
			.get(URL + '/user/profile', {
						headers: {
							"Content-Type": "application/json",
              "Accept": "application/json",
							"Cookie": cookie,
						}
				})
			.inspectJSON()
			.toss();
}).toss();
