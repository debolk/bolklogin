<?php

use OAuth2\Server;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Resource {

	protected Server $server;
	protected array $groups = [];

	function __construct(Server $server, array $groups) {
		$this->server = $server;
		foreach ($groups as $group) {
			$this->groups[] = $group . ',' . LdapHelper::Connect()->getBaseDn();
		}
	}

	/**
	 * @throws Exception
	 */
	public function checkAuthorized(Request $request, Response $response, array $args): Response {
		$req = \OAuth2\Request::createFromGlobals();

		//verify the request
		if (!$this->server->verifyResourceRequest($req)) {
			return ResponseHelper::convertFromOAuth($this->server->getResponse());
		}

		$token = $this->server->getAccessTokenData($req);
		$uid = $token['user_id'];

		$ldap = LdapHelper::Connect();

		//check all groups of the resource, if the user is not in any of the groups, return not authorized
		foreach($this->groups as $group) {
			if ($ldap->memberOf($group, $uid)) {
				return ResponseHelper::create($response, 200, 'OK');
			}
		}
		return ResponseHelper::create($response, 403, json_encode([
			'error' => 'unauthorized',
			'error_description' => 'The user is not authorized to do this'
		]));
	}

}