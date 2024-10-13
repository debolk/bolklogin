<?php

// Determine debug settings
if (getenv('DEBUG')) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
else {
    ini_set('display_errors', 0);
    error_reporting(E_ERROR);
}

require_once '../vendor/autoload.php';

try {
    $pdo = new PDO(getenv('STORAGE_DSN')
    , getenv('STORAGE_USER')
    , getenv('STORAGE_PASS'));
    $storage = new OAuth2\Storage\Pdo($pdo);
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
    'id_lifetime'     => $limit,
    'access_lifetime' => $limit,
]);
$app->server = $server;

// All available grant types
$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, [
    'always_issue_new_refresh_token' => true
]));

$app->
