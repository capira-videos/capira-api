<?php
class ContentTypes extends \Slim\Middleware {
	public function call() {
		// Get reference to application
		$app = $this->app;

		// Decode request body
		$env = $app->environment();
		$env['slim.input'] = json_decode($env['slim.input']);

		// Run inner middleware and application
		$this->next->call();

		// Encode response body
		$res = $app->response;
		$body = $res->getBody();
		$res->setBody(json_encode($body));
	}
}