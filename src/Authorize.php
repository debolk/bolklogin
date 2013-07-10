<?php
/**
 * @uri /authorize
 */
class Authorize extends Tonic\Resource {

    /**
     * @method GET
     */
    function getAuthorize() {
        if(!isset($_SERVER['PHP_AUTH_USER'])) {
            $result = new Tonic\Response(401, "Inloggen is verplicht!");
            $result->wwwAuthenticate = 'Basic realm="De Bolk"';
            return $result;
        }

        return ResourceHelper::OutputToResponse(function() {

            $req = OAuth2\Request::createFromGlobals();
            $response = new OAuth2\Response();

            if($this->app->server->validateAuthorizeRequest($req, $response))
                $this->app->server->handleAuthorizeRequest($req, $response, true);

            $response->send();
        });
    }
}
