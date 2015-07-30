##Install PHP Dependencies
In directory "/api" run:
`php composer.phar install`

##Install Development Tools
Install local dev-dependencies:
`npm install`

To test the API you need jasmine-node:
`npm install jasmine-node -g`

##Run Tests
`jasmine-node --config URL "http://<THE API SERVER URL>" test`

##Build Project for Deployment
`gulp`

##Generate Documentation
`gulp apidoc`
