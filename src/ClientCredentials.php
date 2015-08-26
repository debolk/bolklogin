<?php
/**
 * @uri /client
 */
class ClientCredentials extends Tonic\Resource {

    /**
     * This resource endpoint provides the Client Credentials Grant
     * https://tools.ietf.org/html/rfc6749#section-4.4
     *
     * @method GET
     */
    function getClient() {

        // Must supply username and password over basic auth
        if( !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            $result = new Tonic\Response(401, "Client must authenticate using its credentials over basic authentication");
            $result->wwwAuthenticate = 'Basic realm="De Bolk OAuth Client Credentials"';
            return $result;
        }

        // Return token response
        return ResourceHelper::OutputToResponse(function() {

            $req = OAuth2\Request::createFromGlobals();
            $response = new OAuth2\Response();

            if($this->app->server->validateAuthorizeRequest($req, $response))
            {

                $this->app->server->handleAuthorizeRequest($req, $response, true, $_SERVER['PHP_AUTH_USER']);
            }

            $response->send();
        });
    }
}
