<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class GroupResource extends Resource {
    function __construct(OAuth2\Server $server) {
        $this->server = $server;
        $this->method = 'OPTIONS, GET, POST';
    }

    /**
     * 
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     * 
     * @throws Exception
     */
    public function checkAuthorized(Request $request, Response $response, array $args): Response{
        
        $token = parent::getToken($request, $response);
		if (!is_array($token)) {
			return $token;
		}

		$uid = $token['user_id'];

		$ldap = LdapHelper::Connect();

        $group = $ldap->findGroup($args['group']);

        if (!$group) {
            return ResponseHelper::create($response, '404', json_encode([
                'error' => 'group_not_found',
                'error_description' => 'The requested group "' . $args['group'] . '" could not be found.'
            ]));
        } elseif ($ldap->memberOf($group, $uid)) {
            return ResponseHelper::json($response, json_encode([
					'access_token' => $token['access_token'],
					'user_id' => $token['user_id'],
					'expires' => date("c", $token['expires']),
				]));
        }

        return ResponseHelper::create($response, 403, json_encode([
			'error' => 'unauthorized',
			'error_description' => 'The user is not a member of this group.'
		]));
    }
}