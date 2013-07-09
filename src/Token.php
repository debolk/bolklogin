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

        ob_start();

        $req = OAuth2\Request::createFromGlobals();
        $response = $this->app->server->handleTokenRequest($req);
        $response->send();
        
        //Convert directly sent headers to response object
        $body = $response->getResponseBody();
        $headers = array();
        foreach(headers_list() as $header)
        {
            $parts = explode(':', $header, 2);
            $headers[strtolower($parts[0])] = $parts[1];
        }

        $result = new Tonic\Response(http_response_code(), $body, $headers);
        
        ob_end_clean();

        return $result;
    }
}
