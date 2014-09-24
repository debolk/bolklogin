<?php

require_once '../vendor/autoload.php';

if (getenv('DEBUG')) {
    ini_set('display_errors', true);
    error_reporting(E_ALL);
}
else {
    ini_set('display_errors', false);
    error_reporting(E_ERROR);
}

$app = new Tonic\Application(array(
	'load' => array('../src/*.php', '../src/resources/*.php'),
));
$storage = new OAuth2\Storage\Pdo(array(
    'dsn' => getenv('STORAGE_DSN'),
    'username' => getenv('STORAGE_USER'),
    'password' => getenv('STORAGE_PASS')
));
$server = new OAuth2\Server($storage);
$app->server = $server;

$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

$request = new Tonic\Request();

$resource = $app->getResource($request);

/**
 * Convert conventional output from OAuth lib to html
 */

$response = $resource->exec();
$response->AccessControlAllowOrigin = '*';
$response->output();
