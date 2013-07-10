<?php

require_once '../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$app = new Tonic\Application(array(
	'load' => '../src/*.php'
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
$response->output();
