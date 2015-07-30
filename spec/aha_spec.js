var request = require('request');



request.get('http://localhost:8888/api/channel/1', function (error, response, body) {
	console.log(error, response, body);
});




describe("Hello world", function() {
  it("says hello", function() {
    expect(true).toEqual(true);
  });
});