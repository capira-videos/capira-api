##Install PHP Dependencies
In directory "/api" run:
`php composer.phar install`

##Install Development Tools
Install local dev-dependencies:
`npm install`

To test the API you need jasmine-node 2.0:
`npm install -g jasmine-node@2.0.0-beta4`

##Run Tests
Run all tests on capira.de: 
`jasmine-node spec`

Run all tests on any server:
`jasmine-node --config URL "http://<THE API SERVER URL>" spec`
Example with local MAMP:
`jasmine-node --config URL "http://localhost:8888" spec`

##Build Project for Deployment
`gulp`

##Generate Documentation
`gulp apidoc`
