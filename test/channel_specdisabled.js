'use strict';
var frisby = require('frisby');
var url = process.env.URL;

frisby.create('GET channel 1')
  .get(url + '/channel/1')
  .expectStatus(200)
  .expectHeaderContains('content-type', 'text/html;charset=UTF-8')
  .expectJSON({
    id: 1,
    title: 'Mathe-Crashkurs'
  }) 
  .afterJSON( function(channel) {
    expect(channel.id).toEqual(1);
  })
.toss();
