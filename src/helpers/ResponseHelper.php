<?php


use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Response;

class ResponseHelper
{

	/**
	 * @param \OAuth2\Response $response
	 * @return Response
	 */
	public static function convertFromOAuth(\OAuth2\Response $response): Response {
		$new_response = new Response();
		$new_response = $new_response->withStatus($response->getStatusCode(), $response->getStatusText());
		$new_response = self::set_headers($new_response, $response->getHttpHeaders());
		$res = self::json($new_response, $response->getResponseBody('json'));
		
		syslog(LOG_DEBUG, "----- INCOMING REQUEST -----");
		syslog(LOG_DEBUG, "SERVER --> " . var_export($_SERVER, true));
		syslog(LOG_DEBUG, "GET --> " . var_export($_GET, true));
		$post = $_POST;
		$post['password'] = null;
		syslog(LOG_DEBUG, "POST --> " . var_export($post, true));
		syslog(LOG_DEBUG, "SESSION --> " . var_export($_SESSION, true));
		syslog(LOG_DEBUG, "OAUTH RESPONSE --> " . var_export($response->getHttpHeaders(), true));
		syslog(LOG_DEBUG, "PSR7 RESPONSE --> " . var_export($res->getHeaders(), true));
		syslog(LOG_DEBUG, "----- REQUEST COMPLETE -----");

		return $res;
	}

    public static function create(Response $response, int $code, string $message, string $contentType = "text/plain"): Response
    {
        $new_response = $response->withStatus($code);

        return ResponseHelper::data($new_response, $message, $contentType);
    }

    public static function data(Response $response, mixed $payload, string $type) : Response {
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", $type);
    }

    public static function json(Response $response, string $json): Response {
	    $response = $response->withHeader("Content-Type", 'application/json');
		$response = $response->withStatus(200);
		$response->getBody()->write($json);
        return $response;
    }

	public static function option(Response $response, string $method): Response {
		return $response->withStatus(204)->withHeader('Allow', $method);
	}

	public static function set_headers(Response $response, array $headers) : Response {
		foreach ($headers as $k => $v) {
			$response = $response->withAddedHeader($k, $v);
		}
		return $response;
	}

}