<?php

use \OAuth2\Server;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class ControllerBase {

	protected Server $server;
	protected Response $response;

	protected array $options = [
		'Access-Control-Allow-Origin' => '*',
		'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS',
		'Access-Control-Allow-Headers' => 'Content-Type',
		'Access-Control-Max-Age' => '1728000', // 20 days
		'Content-Length' => '0',
		'Content-Type' => 'text/plain charset=UTF-8'
		];

	function __construct(Server $server) {
		$this->server = $server;
	}

	public function options(Request $request, Response $response, array $args): Response {
		return ResponseHelper::set_headers($response, $this->options);
	}

	public function process(Request $request, Response $response, array $args): Response {
		return ResponseHelper::set_headers($this->process_request($request, $response, $args), [
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS',
			'Access-Control-Allow-Headers' => 'Content-Type']);
	}

	abstract function process_request(Request $request, Response $response, array $args) : Response;
}