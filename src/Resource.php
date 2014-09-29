<?php
/**
 * @uri /resource
 */
class Resource extends Tonic\Resource {

    /**
     * @method GET
     */
    function getAccessTokenInformation()
    {
        $request = OAuth2\Request::createFromGlobals();

        // Validate request, returning error if it's not
        if(!$this->app->server->verifyResourceRequest($request))
        {
            $response = ResourceHelper::OutputToResponse(function() {
                    $this->app->server->getResponse()->send();
            });
            return $response;
        }

        // Return crucial token data if the access_token is valid
        $token = $this->app->server->getAccessTokenData($request);
        return new Tonic\Response(200, json_encode([
            'access_token' => $token['access_token'],
            'user_id'      => $token['user_id'],
            'expires'      => date("c", $token['expires']),
        ]));
    }
}
