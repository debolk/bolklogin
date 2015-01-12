<?php
/**
 * @uri /token
 */
class Token extends Tonic\Resource {

    /**
     * @method OPTIONS
     */
    function options()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET');
        header('Access-Control-Allow-Headers: Authorization');
        header('Access-Control-Max-Age: 1');  //1728000
        header("Content-Length: 0");
        header("Content-Type: text/plain charset=UTF-8");
        exit(0);
    }

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
