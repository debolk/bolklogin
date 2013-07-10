<?php
/**
 * @uri /token
 */
class Token extends Tonic\Resource {

    /**
     * @method POST
     * @method GET
     */
    function postToken() {
        return ResourceHelper::OutputToResponse(function() {

            $req = OAuth2\Request::createFromGlobals();
            $response = $this->app->server->handleTokenRequest($req);
            $response->send();

        });
    }
}
