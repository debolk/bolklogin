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
