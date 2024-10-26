<?php

// Determine debug settings
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Handlers\Strategies\RequestResponseArgs;

if (getenv('DEBUG')) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}
else {
	ini_set('display_errors', 0);
	error_reporting(E_ERROR);
}

require_once '../vendor/autoload.php';
require('../src/helpers/LdapHelper.php');
require('../src/helpers/ResponseHelper.php');
require('../src/controllers/ControllerBase.php');
require('../src/controllers/ControllerAuthorize.php');
require('../src/controllers/ControllerResource.php');
require('../src/controllers/ControllerToken.php');
require('../src/Resource.php');

// Bootstrap application
$app = Slim\Factory\AppFactory::create();

try {
	$storage = new \OAuth2\Storage\Pdo(array(
		'dsn' => getenv('STORAGE_DSN'),
		'username' => getenv('STORAGE_USER'),
		'password' => getenv('STORAGE_PASS'))
	);
}
catch (PDOException $e) {
	if (getenv('DEBUG')) {
		echo $e->getMessage();
	}
	else {
		echo 'Cannot connect to database';
	}
	exit(1);
}

// Limit access token times which are acceptable
$limit = max(1, min(7200, (int)getenv('OAUTH_TOKEN_LIFETIME')));

// Setup server
$server = new OAuth2\Server($storage, [
	'id_lifetime'	 => $limit,
	'access_lifetime' => $limit,
]);

// All available grant types
$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, [
	'always_issue_new_refresh_token' => true
]));

$app->
=======
//initialise Resource classes
$authenticate = new ControllerAuthorize($server);
$resource = new ControllerResource($server);
$token = new ControllerToken($server);
//access levels
$bekend = new Resource($server, [
	'ou=people,ou=kandidaatleden,o=nieuwedelft',
	'ou=people,ou=oudleden,o=nieuwedelft',
	'ou=people,ou=ereleden,o=nieuwedelft',
	'ou=people,ou=leden,o=nieuwedelft',
	'ou=people,ou=ledenvanverdienste,o=nieuwedelft'
]);
$lid = new Resource($server, [
	'ou=people,ou=leden,o=nieuwedelft',
	'ou=people,ou=ereleden,o=nieuwedelft',
	'ou=people,ou=ledenvanverdienste,o=nieuwedelft',
]);
$ictcom = new Resource($server, [
	'cn=ictcom,ou=groups,o=nieuwedelft',
	'cn=bestuur,ou=groups,o=nieuwedelft',
	'cn=beheer,ou=groups,o=nieuwedelft'
]);
$bestuur = new Resource($server, [
	'cn=bestuur,ou=groups,o=nieuwedelft',
	'cn=beheer,ou=groups,o=nieuwedelft'
]);

$app->getRouteCollector()->setDefaultInvocationStrategy(new RequestResponse());

//get readme html
$app->get('/', 'Readme::getIndex');

/**
 * @deprecated /authenticate will be replaced by /authorize to adhere to the bshaffer/OAuth2 naming convention.
 *              Applications should replace the uri.
 */
//redirect to this with response_type=code client_id, redirect_uri & state
$app->get('/authenticate', [$authenticate, 'process']);
$app->post('/authenticate', [$authenticate, 'process']);
$app->options('/authenticate', [$authenticate, 'options']);

//redirect to this with response_type=code client_id, redirect_uri & state
$app->get('/authorize', array($authenticate, 'process'));
$app->post('/authorize', array($authenticate, 'process'));
$app->options('/authorize', [$authenticate, 'options']);

//validate access token with access_token
$app->get('/resource', array($resource, 'process'));
$app->options('/resource', [$resource, 'options']);

//get access token from server with client_id and client_secret in json body
$app->get('/token', array($token, 'process'));
$app->post('/token', array($token, 'process'));
$app->options('/token', [$token, 'options']);

//is a resource; access_token
$app->get('/bekend', [$bekend, 'checkAuthorized']);
$app->post('/bekend', [$bekend, 'checkAuthorized']);
$app->options('/bekend', [$bekend, 'options']);

//is a resource; access_token
$app->get('/lid', [$lid, 'checkAuthorized']);
$app->post('/lid', [$lid, 'checkAuthorized']);
$app->options('/lid', [$lid, 'options']);

//is a resource; access_token
$app->get('/ictcom', [$ictcom, 'checkAuthorized']);
$app->post('/ictcom', [$ictcom, 'checkAuthorized']);
$app->options('/ictcom', [$ictcom, 'options']);

//is a resource; access_token
$app->get('/bestuur', [$bestuur, 'checkAuthorized']);
$app->post('/bestuur', [$bestuur, 'checkAuthorized']);
$app->options('/bestuur', [$bestuur, 'options']);

$app->run();
