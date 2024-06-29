<?php

use OAuth2\Server;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ControllerResource extends ControllerBase {

	function __construct(Server $server) {
		$this->options['Access-Control-Allow-Methods'] = 'GET, OPTIONS';
		parent::__construct($server);
	}

	public function process_request(Request $request, Response $response, array $args): Response {
		$req = OAuth2\Request::createFromGlobals();

		//validate request, otherwise return error response
		if (!$this->server->verifyResourceRequest($req)){
			return ResponseHelper::convertFromOAuth($this->server->getResponse());
		}

		//send token for client to verify
		$token = $this->server->getAccessTokenData($req);
		return ResponseHelper::json($response, json_encode([
			'access_token' => $token['access_token'],
			'user_id' => $token['user_id'],
			'expires' => date("c", $token['expires']),
		]));
	}

}