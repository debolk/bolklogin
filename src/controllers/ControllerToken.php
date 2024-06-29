<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * @uri /token
 */
class ControllerToken extends ControllerBase {

	public function process_request(Request $request, Response $response, array $args): Response {
		$req = OAuth2\Request::createFromGlobals();
		$res = $this->server->handleTokenRequest($req);
		return ResponseHelper::convertFromOAuth($res);
	}

}
