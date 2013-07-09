<?php

require_once '../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$app = new Tonic\Application(array(
	'load' => '../src/*.php'
));

$request = new Tonic\Request();

$resource = $app->getResource($request);
$response = $resource->exec();
$response->output();
