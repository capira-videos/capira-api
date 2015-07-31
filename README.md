##Install PHP Dependencies
In directory "/api" run:
`php composer.phar install`

##Install Development Tools
Install local dev-dependencies:
`npm install`

To test the API you need jasmine-node:
`npm install jasmine-node -g`

##Run Tests
Run all tests on capira.de: 
`jasmine-node spec`

Run all tests on any server:
`jasmine-node --config URL "http://<THE API SERVER URL>" spec`

##Build Project for Deployment
`gulp`

##Generate Documentation
`gulp apidoc`
