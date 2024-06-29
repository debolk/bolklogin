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
		return self::json($new_response, $response->getResponseBody('json'));
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
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", 'application/json');
    }

	public static function set_headers(Response $response, array $headers) : Response {
		foreach ($headers as $k => $v) {
			$response = $response->withAddedHeader($k, $v);
		}
		return $response;
	}

}